<?php

namespace App\Http\Controllers;

use App\Models\DeliveryMethod;
use App\Models\OrderFulfillment;
use App\Models\OrderFulfillmentItem;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Mail\InvoiceMail;
use App\Mail\OrderCancelledMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index()
    {
        $freeDeliveryLimit = Setting::where('key', 'min_order_free_delivery')->value('value') ?? 0;

        $orders = Order::with('user')
            ->withSum('items', 'quantity')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($order) use ($freeDeliveryLimit) {
                $hasReceipt = OrderFulfillment::where('order_id', $order->order_id)->exists();

                $deliveryCost = 0;
                $deliveryMethodName = 'Free';
                $deliveryMethod = DeliveryMethod::where('delivery_method_id', $order->delivery_method_id)->first();

                if ($order->product_total_amount < $freeDeliveryLimit) {
                    if ($deliveryMethod && $deliveryMethod->delivery_method_amount > 0) {
                        $deliveryCost = $deliveryMethod->delivery_method_amount;
                        $deliveryMethodName = $deliveryMethod->delivery_method_name;
                    }
                } else {
                    $deliveryMethodName = 'Free (' . $deliveryMethod->delivery_method_name . ')';
                }

                return [
                    'order_id' => $order->order_id,
                    'created_at' => $order->created_at->toDateTimeString(),
                    'name' => optional($order->user)->name,
                    'total_paid' => (float) $order->total_paid,
                    'status' => $order->status,
                    'fulfillment_status' => $order->fulfillment_status,
                    'total_items' => (int) $order->items_sum_quantity,
                    'delivery_method' => $deliveryMethodName,
                    'has_receipt' => $hasReceipt,
                ];
            });

        return response()->json([
            'status' => true,
            'orders' => $orders,
        ], 200);
    }

    public function updateOrderStatus(Request $request)
    {
        $request->validate([
            'order_ids' => 'required',
            'status' => 'required|in:Pending,Paid,Cancelled',
        ]);

        Order::whereIn('order_id', $request->order_ids)
            ->update(['status' => $request->status]);

        return response()->json(['success' => true]);
    }

    public function showByOrderId($order_id)
    {
        $receipt = OrderFulfillment::where('order_id', $order_id)->first();

        if (!$receipt) {
            return response()->json(['status' => false, 'message' => 'Receipt not found']);
        }

        return response()->json(['status' => true, 'receipt' => $receipt]);
    }

    public function cancelOrder(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,order_id',
        ]);

        $order = Order::findOrFail($request->order_id);

        if ($order->status === 'cancelled') {
            return response()->json(['status' => false, 'message' => 'Order is already cancelled.']);
        }

        $order->status = 'cancelled';
        $order->save();

        Mail::to($order->user->email)->send(new OrderCancelledMail($order));

        return response()->json([
            'status' => true,
            'message' => 'Order cancelled successfully.',
        ]);
    }

    public function ordersBulkMarkStatus(Request $req)
    {
        $req->validate([
            'order_ids' => 'required|array',
            'bulkstatus' => 'required|string|in:paid,cancelled'
        ]);

        $orders = Order::with('user:id,email')->whereIn('order_id', $req->order_ids)->get();

        $updatedCount = 0;
        $creditedCount = 0;

        foreach ($orders as $order) {
            DB::transaction(function () use ($req, $order, &$updatedCount, &$creditedCount) {
                $targetStatus = strtolower($req->bulkstatus);
                $current = strtolower((string) $order->status);

                if ($current !== $targetStatus) {
                    $order->status = $targetStatus;
                    $order->save();
                    $updatedCount++;
                }

                if ($targetStatus === 'cancelled') {
                    if ($order->relationLoaded('user') && $order->user && $order->user->email) {
                        Mail::to($order->user->email)->send(new OrderCancelledMail($order));
                    }
                    return;
                }

                if ($targetStatus === 'paid' && (bool) $order->pay_by_bank === true) {
                    if ($current !== 'paid') {
                        $wallet = Wallet::lockForUpdate()->firstOrCreate(
                            ['user_id' => $order->user_id],
                            ['balance' => 0]
                        );

                        $walletId = $wallet->wallet_id ?? $wallet->id;
                        $reference = 'PAYBYBANK-BONUS-ORDER-' . $order->order_id;

                        $alreadyCredited = WalletTransaction::where('wallet_id', $walletId)
                            ->where('reference', $reference)
                            ->exists();

                        if (!$alreadyCredited) {
                            $wallet->increment('balance', 1);

                            WalletTransaction::create([
                                'wallet_id' => $walletId,
                                'type' => 'credit',
                                'amount' => 1,
                                'reference' => $reference,
                                'description' => 'Pay by bank bonus (bulk mark paid)',
                            ]);

                            $creditedCount++;
                        }
                    }
                }
            });
        }
        return response()->json([
            'success' => true,
            'updated_count' => $updatedCount,
            'credited_count' => $creditedCount,
        ]);
    }

    public function ordersBulkMarkFulfilled(Request $req)
    {
        $validated = $req->validate([
            'order_ids' => ['required', 'array'],
            'bulkfulfilled' => ['required', 'string', 'in:fulfilled,unfulfilled'],
        ]);

        $summary = DB::transaction(function () use ($validated) {
            $orders = Order::with('items')
                ->whereIn('order_id', $validated['order_ids'])
                ->lockForUpdate()
                ->get();

            $ordersUpdated = 0;
            $itemsUpdated = 0;
            $fulCreated = 0;
            $fulItemsCreated = 0;
            $fulDeleted = 0;

            if ($validated['bulkfulfilled'] === 'fulfilled') {
                foreach ($orders as $order) {
                    $remainingItems = $order->items->filter(function ($it) {
                        $remaining = max(0, ($it->quantity ?? 0) - ($it->fulfilled_quantity ?? 0));
                        return $remaining > 0;
                    });

                    if ($remainingItems->isEmpty()) {
                        if ($order->fulfillment_status !== 'fulfilled') {
                            $order->fulfillment_status = 'fulfilled';
                            $order->save();
                            $ordersUpdated++;
                        }
                        continue;
                    }

                    $ful = OrderFulfillment::create([
                        'order_id' => $order->order_id,
                        'tracking_id' => null,
                        'shipping_courier' => null,
                        'fulfilled_at' => now(),
                    ]);
                    $fulCreated++;

                    foreach ($remainingItems as $it) {
                        $remaining = max(0, ($it->quantity ?? 0) - ($it->fulfilled_quantity ?? 0));
                        if ($remaining <= 0)
                            continue;

                        OrderFulfillmentItem::create([
                            'order_fulfillment_id' => $ful->order_fulfillment_id,
                            'order_item_id' => $it->order_item_id,
                            'quantity' => $remaining,
                        ]);
                        $fulItemsCreated++;

                        $itemsUpdated += $it->update([
                            'fulfilled_quantity' => $it->quantity
                        ]) ? 1 : 0;
                    }

                    if ($order->fulfillment_status !== 'fulfilled') {
                        $order->fulfillment_status = 'fulfilled';
                        $order->save();
                    }
                    $ordersUpdated++;
                }
            } else {
                foreach ($orders as $order) {
                    $itemsUpdated += OrderItem::where('order_id', $order->order_id)
                        ->update(['fulfilled_quantity' => 0]);

                    $fulDeleted += OrderFulfillment::where('order_id', $order->order_id)->delete();

                    if ($order->fulfillment_status !== 'unfulfilled') {
                        $order->fulfillment_status = 'unfulfilled';
                        $order->save();
                    }
                    $ordersUpdated++;
                }
            }

            return [
                'orders_updated' => $ordersUpdated,
                'items_updated' => $itemsUpdated,
                'fulfillments_created' => $fulCreated,
                'fulfillment_items_created' => $fulItemsCreated,
                'fulfillments_deleted' => $fulDeleted,
            ];
        });

        return response()->json(array_merge([
            'status' => true,
            'message' => 'Fulfillment status updated.',
        ], $summary));
    }

    public function orderEditData($orderid)
    {
        $order = Order::with([
            'items.variant.product',
            'items.variant.mvariantDetail',
            'items.variant.mstock',
            'user:id,name,email,mobile',
            'userCompanyAddress',
            'deliveryMethod',
            'coupon',
            'receipts',
            'fulfillments.items.orderItem.variant.product',
            'fulfillments.items.orderItem.variant.mvariantDetail',
        ])->find($orderid);

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        $units = $order->items->sum('quantity');
        $skus = $order->items->count();

        $walletDiscount = $order->wallet_discount ?? 0.00;
        $couponDiscount = $order->coupon_discount + $order->wallet_discount ?? 0.00;

        $deliveryId = optional($order->deliveryMethod)->delivery_method_id;
        $deliveryName = optional($order->deliveryMethod)->delivery_method_name;

        $addressId = optional($order->userCompanyAddress)->user_company_address_id;
        $address = optional($order->userCompanyAddress)->full_address;

        $freeDeliveryLimit = Setting::where('key', 'min_order_free_delivery')->value('value') ?? 0;
        $deliveryCost = 0;
        if ($order->product_total_amount < $freeDeliveryLimit) {
            $deliveryCost = DeliveryMethod::where('delivery_method_id', $deliveryId)->value('delivery_method_amount') ?? 0;
        }

        $items = $order->items->map(function ($itm) {
            $variant = $itm->variant;
            $product = $variant->product;

            $rawOptions = optional($variant->mvariantDetail)->options;
            $rawOptionValue = optional($variant->mvariantDetail)->option_value;

            $parsedOptions = is_string($rawOptions) ? json_decode($rawOptions, true) : (is_array($rawOptions) ? $rawOptions : null);
            $parsedOptionValue = is_string($rawOptionValue) ? json_decode($rawOptionValue, true) : (is_array($rawOptionValue) ? $rawOptionValue : null);

            return [
                'order_item_id' => $itm->order_item_id,
                'mvariant_id' => $variant->mvariant_id,
                'quantity' => (int) $itm->quantity,
                'fulfilled_quantity' => (int) ($itm->fulfilled_quantity ?? 0),
                'unit_price' => (float) $itm->unit_price,

                'variant' => [
                    'sku' => $variant->sku,
                    'image' => $variant->mvariant_image,
                    'price' => (float) $variant->price,
                    'compare_price' => (float) $variant->compare_price,
                    'cost_price' => (float) $variant->cost_price,
                    'weight' => $variant->weight,
                    'weightunit' => $variant->weightunit,
                    'options' => $parsedOptions,
                    'option_value' => $parsedOptionValue,
                    'stock' => optional($variant->mstock)->quantity ?? 0,
                    'mlocation_id' => optional($variant->mstock)->mlocation_id,
                ],

                'product' => [
                    'mproduct_id' => $product->mproduct_id,
                    'mproduct_title' => $product->mproduct_title,
                    'mproduct_slug' => $product->mproduct_slug,
                    'mproduct_image' => $product->mproduct_image,
                ],
            ];
        })->values();

        $fulfillments = $order->fulfillments->map(function ($f) {
            return [
                'order_fulfillment_id' => $f->order_fulfillment_id,
                'tracking_id' => $f->tracking_id,
                'shipping_courier' => $f->shipping_courier,
                'fulfilled_at' => $f->fulfilled_at,

                'items' => $f->items->map(function ($fi) {
                    $oi = $fi->orderItem;
                    $variant = $oi->variant;
                    $product = $variant->product;

                    $rawOptionValue = optional($variant->mvariantDetail)->option_value;
                    $parsedOptionValue = is_string($rawOptionValue)
                        ? json_decode($rawOptionValue, true)
                        : (is_array($rawOptionValue) ? $rawOptionValue : null);

                    return [
                        'order_item_id' => $oi->order_item_id,
                        'quantity' => (int) $fi->quantity,

                        'variant' => [
                            'sku' => $variant->sku,
                            'image' => $variant->mvariant_image,
                            'price' => (float) $variant->price,
                            'option_value' => $parsedOptionValue,
                        ],
                        'product' => [
                            'mproduct_title' => $product->mproduct_title,
                            'mproduct_image' => $product->mproduct_image,
                        ],
                    ];
                })->values(),
            ];
        })->values();

        $payload = [
            'order_id' => $order->order_id,
            'order_number' => '#00' . $order->order_id,
            'user' => [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
                'mobile' => $order->user->mobile,
            ],
            'order_date' => $order->created_at->toDateTimeString(),
            'units' => $units,
            'payment_status' => $order->status,
            'fulfillment_status' => $order->fulfillment_status,
            'skus' => $skus,

            'delivery' => [
                'method_id' => $deliveryId,
                'method' => $deliveryName,
                'address_id' => $addressId,
                'address' => $address,
            ],

            'coupon' => $order->coupon ? [
                'coupon_id' => $order->coupon->coupon_id,
                'code' => $order->coupon->code,
                'discount_type' => $order->coupon->discount_type,
                'discount_value' => $order->coupon->discount_value,
                'expires_at' => $order->coupon->expires_at,
                'usage_limit' => $order->coupon->usage_limit,
                'per_user_limit' => $order->coupon->per_user_limit,
                'min_cart_value' => $order->coupon->min_cart_value,
            ] : null,

            'delivery_instructions' => $order->delivery_instructions,

            'summary' => [
                'subtotal' => $order->product_total_amount,
                'wallet_discount' => $walletDiscount,
                'coupon_discount' => $couponDiscount,
                'delivery_cost' => $deliveryCost,
                'vat' => $order->vat,
                'payment_total' => $order->total_amount,
                'total_paid' => $order->total_paid,
            ],

            'items' => $items,
            'fulfillments' => $fulfillments,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Fetch Order Successfully',
            'order' => $payload,
        ], 200);
    }

    public function markAsPaid(Request $request)
    {
        $order = Order::with('user:id,name,email')->find($request->order_id);

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        if (strtolower($order->status) === 'paid') {
            return response()->json(['status' => true, 'message' => 'Order is already paid']);
        }

        DB::transaction(function () use ($order) {
            $order->status = 'paid';
            $order->save();

            if ((bool) $order->pay_by_bank === true) {
                $wallet = Wallet::lockForUpdate()->firstOrCreate(
                    ['user_id' => $order->user_id],
                    ['balance' => 0]
                );

                $walletId = $wallet->wallet_id ?? $wallet->id;

                $reference = 'PAYBYBANK-BONUS-ORDER-' . $order->order_id;

                $alreadyCredited = WalletTransaction::where('wallet_id', $walletId)
                    ->where('reference', $reference)
                    ->exists();

                if (!$alreadyCredited) {
                    $wallet->increment('balance', 1);

                    WalletTransaction::create([
                        'wallet_id' => $walletId,
                        'type' => 'credit',
                        'amount' => 1,
                        'reference' => $reference,
                        'description' => 'Pay by bank bonus on mark as paid',
                    ]);
                }
            }
        });

        return response()->json(['status' => true, 'message' => 'Order marked as paid']);
    }

    public function sendInvoice(Request $request)
    {
        $request->validate([
            'order_id' => 'required|integer|exists:orders,order_id',
        ]);

        $order = Order::with('user')->where('order_id', $request->order_id)->firstOrFail();

        try {
            Mail::to($order->user->email)->send(new InvoiceMail($order));

            return response()->json([
                'status' => true,
                'message' => 'Invoice email sent successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to send invoice.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function packingSlip($order_id)
    {
        $order = Order::with([
            'user',
            'userCompanyAddress',
            'items.variant.product',
            'items.variant.details',
            'fulfillments' => function ($q) {
                $q->orderBy('fulfilled_at', 'asc');
            },
            'fulfillments.items.orderItem.variant.product',
            'fulfillments.items.orderItem.variant.details',
        ])->findOrFail($order_id);

        return view('admin.orders.packing-slip', compact('order'));
    }

    public function bulkPackingSlips(Request $request)
    {
        // order_ids: [14,15,16,...]  (array या CSV दोनों चलेंगे)
        $ids = $request->input('order_ids', []);
        if (is_string($ids)) {
            $ids = array_filter(array_map('intval', explode(',', $ids)));
        } else {
            $ids = array_filter(array_map('intval', (array) $ids));
        }

        $request->validate([
            'order_ids' => ['required'],
            // अगर strict चाहिए तो: 'order_ids.*' => 'integer|exists:orders,order_id'
        ]);

        $orders = Order::with([
            'user',
            'userCompanyAddress',
            'items.variant.product',
            'items.variant.details',
            'fulfillments' => function ($q) {
                $q->orderBy('fulfilled_at', 'asc');
            },
            'fulfillments.items.orderItem.variant.product',
            'fulfillments.items.orderItem.variant.details',
        ])
            ->whereIn('order_id', $ids)
            ->orderBy('order_id', 'asc')
            ->get();

        if ($orders->isEmpty()) {
            abort(404, 'No orders found for given IDs.');
        }

        return view('admin.orders.bulk-packing-slip', compact('orders'));
    }

    public function fulfill(Request $req)
    {
        $data = $req->validate([
            'order_id' => 'required|integer|exists:orders,order_id',
            'lines' => 'required|array',
            'lines.*.order_item_id' => 'required|integer|exists:order_items,order_item_id',
            'lines.*.quantity' => 'required|integer|min:1',
        ]);

        return DB::transaction(function () use ($data) {
            $order = Order::with('items')->findOrFail($data['order_id']);

            $open = OrderFulfillment::where('order_id', $order->order_id)
                ->whereNull('tracking_id')
                ->first();

            if (!$open) {
                $open = OrderFulfillment::create([
                    'order_id' => $order->order_id,
                    'fulfilled_at' => now(),
                ]);
            }

            foreach ($data['lines'] as $line) {
                /** @var OrderItem $oi */
                $oi = OrderItem::findOrFail($line['order_item_id']);

                $remaining = ($oi->quantity ?? 0) - ($oi->fulfilled_quantity ?? 0);
                $qty = min($remaining, (int) $line['quantity']);
                if ($qty <= 0) {
                    continue;
                }

                OrderFulfillmentItem::create([
                    'order_fulfillment_id' => $open->order_fulfillment_id,
                    'order_item_id' => $oi->order_item_id,
                    'quantity' => $qty,
                ]);

                $oi->increment('fulfilled_quantity', $qty);
            }

            $all = $order->items()->get();
            $allDone = $all->every(fn($i) => ($i->fulfilled_quantity ?? 0) >= ($i->quantity ?? 0));
            $order->fulfillment_status = $allDone ? 'fulfilled' : 'unfulfilled';
            $order->save();

            $order->load([
                'items.variant.product',
                'fulfillments.items.orderItem.variant.product',
            ]);

            return response()->json([
                'status' => true,
                'order' => $order,
            ]);
        });
    }

    public function addTracking(Request $req)
    {
        $data = $req->validate([
            'order_fulfillment_id' => 'required|integer|exists:order_fulfillments,order_fulfillment_id',
            'tracking_id' => 'required|string|max:255',
            'shipping_courier' => 'nullable|string|max:255',
        ]);

        $f = OrderFulfillment::findOrFail($data['order_fulfillment_id']);
        $f->update([
            'tracking_id' => $data['tracking_id'],
            'shipping_courier' => $data['shipping_courier'],
        ]);

        return response()->json(['status' => true]);
    }

}
