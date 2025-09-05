<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart_item;
use App\Models\CouponUsage;
use App\Models\Customer;
use App\Models\CustomerCommission;
use App\Models\DeliveryMethod;
use App\Models\Mstock;
use App\Models\Order;
use App\Models\OrderCommission;
use App\Models\OrderItem;
use App\Models\Setting;
use App\Models\Wallet;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Mail\OrderPlacedMail;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->query('per_page', 10);
        $perPage = min($perPage, 100);

        $query = Order::with([
            'items.variant.product',
            'user:id,name,email',
            'userCompanyAddress',
            'deliveryMethod',
            'coupon'
        ])->where('user_id', auth()->id())
        ->orderBy('created_at', 'desc');

        $paginator = $query->paginate($perPage);

        $payload = $paginator->getCollection()->map(function($order) {
            $units = $order->items->sum('quantity');
            $skus  = $order->items->count();

            $walletDiscount = $order->wallet_discount ?? 0;
            $couponDiscount = $order->coupon_discount ?? 0;

            $delivery = $order->deliveryMethod;
            $address  = $order->userCompanyAddress;

            $freeDeliveryLimit = Setting::where('key', 'min_order_free_delivery')->value('value') ?? 0;
            $deliveryCost = '0';

            if ($order->product_total_amount < $freeDeliveryLimit) {
                $deliveryCost = DeliveryMethod::where('delivery_method_id', $delivery->delivery_method_id)->value('delivery_method_amount') ?? 0;
            }

            $items = $order->items->map(function($itm) {
                $variant = $itm->variant;
                $product = $variant->product;

                $opts = $variant->mvariantDetail->options        ?? null;
                $vals = $variant->mvariantDetail->option_value   ?? null;
                $opts = is_string($opts) ? json_decode($opts, true) : $opts;
                $vals = is_string($vals) ? json_decode($vals, true) : $vals;

                return [
                    'order_item_id' => $itm->order_item_id,
                    'mvariant_id'   => $variant->mvariant_id,
                    'quantity'      => $itm->quantity,
                    'unit_price'    => (float) $itm->unit_price,
                    'variant'       => [
                        'sku'           => $variant->sku,
                        'image'         => $variant->mvariant_image,
                        'price'         => (float) $variant->price,
                        'compare_price' => (float) $variant->compare_price,
                        'cost_price'    => (float) $variant->cost_price,
                        'options'       => $opts,
                        'option_value'  => $vals,
                    ],
                    'product'       => [
                        'mproduct_id'    => $product->mproduct_id,
                        'mproduct_title' => $product->mproduct_title,
                        'mproduct_slug'  => $product->mproduct_slug,
                        'mproduct_image' => $product->mproduct_image,
                    ],
                ];
            });

            return [
                'order_id'   => $order->order_id,
                'user'       => [
                    'id'    => $order->user->id,
                    'name'  => $order->user->name,
                    'email' => $order->user->email,
                ],
                'order_date'           => $order->created_at->toDateTimeString(),
                'units'                => $units,
                'payment_status'       => $order->status,
                'fulfillment_status'   => $order->fulfillment_status,
                'skus'                 => $skus,
                'delivery'             => [
                    'method_id'  => $delivery->delivery_method_id   ?? null,
                    'method'     => $delivery->delivery_method_name ?? null,
                    'address_id' => $address->user_company_address_id ?? null,
                    'address'    => $address->full_address          ?? null,
                ],
                'coupon' => $order->coupon ? [
                    'coupon_id'      => $order->coupon->coupon_id,
                    'code'           => $order->coupon->code,
                    'discount_type'  => $order->coupon->discount_type,
                    'discount_value' => $order->coupon->discount_value,
                    'expires_at'     => $order->coupon->expires_at,
                    'usage_limit'    => $order->coupon->usage_limit,
                    'per_user_limit' => $order->coupon->per_user_limit,
                    'min_cart_value' => $order->coupon->min_cart_value,
                ] : null,
                'delivery_instructions' => $order->delivery_instructions,
                'summary'   => [
                    'subtotal'        => $order->product_total_amount, 
                    'wallet_discount' => $walletDiscount, 
                    'coupon_discount' => $couponDiscount, 
                    'delivery_cost'   => $deliveryCost, 
                    'vat'             => $order->vat, 
                    'payment_total'   => $order->total_amount, 
                    'total_paid'      => $order->total_paid, 
                ],
                'items' => $items,
            ];
        });

        $paginator->setCollection($payload);

        return response()->json([
            'status'  => true,
            'message' => 'Fetch all Orders Successfully',
            'cdnURL'     => config('cdn.url'),
            'orders'  => $paginator->items(),      
            'meta'    => [
                'current_page' => $paginator->currentPage(),
                'per_page'     => $paginator->perPage(),
                'total'        => $paginator->total(),
                'last_page'    => $paginator->lastPage(),
                'from'         => $paginator->firstItem(),
                'to'           => $paginator->lastItem(),
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'wallet_discount'           => ['nullable', 'numeric'],
            'coupon_discount'           => ['nullable', 'numeric'],
            'user_company_address_id'   => ['required', 'integer', 'exists:user_company_addresses,user_company_address_id'],
            'delivery_method_id'        => ['required', 'integer', 'exists:delivery_methods,delivery_method_id'],
            'delivery_instructions'     => ['nullable', 'string'],
            'coupon_id'                 => ['nullable','string','regex:/^\d+$/'],

        ]);
            $couponId = $validated['coupon_id'] ?? null;
            if ($couponId === '') {
                $couponId = null;
            }

        DB::beginTransaction();

        try {
            $cartItems = Cart_item::where('user_id', $user->id)
                ->with('mvariant') 
                ->get();

            if ($cartItems->isEmpty()) {
                return response()->json([
                    'status' => false,
                    'message' => 'Cart is empty.',
                ], 422);
            }

            $productTotal = 0;
            $totalvat = 0;

            foreach ($cartItems as $cart) {
                $quantity = $cart->quantity;
                $unit_price = $cart->mvariant->price;
                $vatAmount = 0;

                if ($cart->mvariant->taxable == 1) {
                    $vatAmountsum = $unit_price * 0.20;
                    $vatAmount += $vatAmountsum;
                }

                $lineSubtotal = $unit_price * $quantity;
                $productTotal += $lineSubtotal;
                $totalvat += $vatAmount * $quantity;
            }

            $freeDeliveryLimit = Setting::where('key', 'min_order_free_delivery')->value('value') ?? 0;
            $deliveryCharge = 0;

            if ($productTotal < $freeDeliveryLimit) {
                $deliveryCharge = DeliveryMethod::where('delivery_method_id', $validated['delivery_method_id'])->value('delivery_method_amount') ?? 0;
            }

            $wallet_discount = $validated['wallet_discount'] ?? 0;
            $coupon_discount = $validated['coupon_discount'] ?? 0;

            $finalTotal = $productTotal + $totalvat + $deliveryCharge - $wallet_discount - $coupon_discount;
            $totalPaid = $productTotal + $totalvat + $deliveryCharge - $coupon_discount;

            $wallet = Wallet::where('user_id', $user->id)->first();

            if ($wallet_discount > $wallet->balance) {
                return response()->json(['status' => false, 'message' => 'Insufficient wallet balance'], 400);
            }

            if ($wallet && $wallet_discount > 0) {
                $wallet->balance =  $wallet->balance - $wallet_discount;
                $wallet->save();

                WalletTransaction::create([
                    'wallet_id'   => $wallet->wallet_id,
                    'type'        => 'debit',
                    'amount'      => $wallet_discount,
                    'reference'   => 'ORDER-' . uniqid(), 
                    'description' => 'Wallet used during checkout',
                ]);
            }

            $status = $finalTotal > 0 ? 'pending' : 'paid';

            $order = Order::create([
                'user_id'                  => $user->id,
                'total_amount'             => $finalTotal,
                'wallet_discount'          => $wallet_discount,
                'coupon_discount'          => $coupon_discount,
                'status'                   => $status,
                'fulfillment_status'       => 'unfulfilled',
                'user_company_address_id'  => $validated['user_company_address_id'],
                'delivery_method_id'       => $validated['delivery_method_id'],
                'vat'                      => $totalvat,
                'total_paid'               => $totalPaid,
                'product_total_amount'     => $productTotal,
                'delivery_instructions'    => $validated['delivery_instructions'],
                'coupon_id' => $couponId,
            ]);

            foreach ($cartItems as $cart) {
                $quantity = $cart->quantity;
                $unit_price = $cart->mvariant->price;

                if ($cart->mvariant->taxable == 1) {
                    $unit_price += $unit_price * 0.20;
                }

                OrderItem::create([
                    'order_id'    => $order->order_id,
                    'mvariant_id' => $cart->mvariant_id,
                    'quantity'    => $quantity,
                    'unit_price'  => $unit_price,
                ]);

                Mstock::where('mvariant_id', $cart->mvariant_id)
                    ->decrement('quantity', $quantity);
            }

            Cart_item::where('user_id', $user->id)->delete();

            if ($user->rep_id) {
                $rep_id = $user->rep_id;

                $commissionPercent = Customer::where('rep_id', $rep_id)->value('commission_percent') ?? 0;

                $commissionAmount = ($productTotal * $commissionPercent) / 100;

                OrderCommission::create([
                    'order_id'           => $order->order_id,
                    'rep_id'             => $rep_id,
                    'user_id'            => $user->id,
                    'product_total'      => $productTotal,
                    'commission_percent' => $commissionPercent,
                    'commission_amount'  => $commissionAmount,
                ]);

                CustomerCommission::updateOrCreate(
                    ['rep_id' => $rep_id],
                    ['total_commission' => DB::raw("total_commission + $commissionAmount")]
                );
            }

            if (! is_null($couponId)) {
                CouponUsage::updateOrCreate(
                    ['coupon_id' => $couponId, 'user_id' => $user->id],
                    ['used_count' => DB::raw('used_count + 1')]
                );
            }

            DB::commit();

            $order->load(['items', 'user:id,name,email']);

            Mail::to($order->user->email)->queue(new OrderPlacedMail($order));

            return response()->json([
                'status'  => true,
                'message' => 'Order Placed Successfully',
                'cdnURL'     => config('cdn.url'),
                'order'   => $order,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create order.',
                'error'   => $e->getMessage(),
            ], 500);
        }
    }

    public function show($id)
    {
        $order = Order::with([
            'items.variant.product',
            'items.variant.mvariantDetail',    
            'items.variant.mstock',          
            'user:id,name,email',                                                                  
            'userCompanyAddress',
            'deliveryMethod',
            'coupon'
        ])->find($id);

        if (! $order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        $units = $order->items->sum('quantity');
        $skus  = $order->items->count();

        $walletDiscount = $order->wallet_discount ?? 0.00;
        $couponDiscount = $order->coupon_discount ?? 0.00;

        $deliveryId = optional($order->deliveryMethod)->delivery_method_id;
        $deliveryName = optional($order->deliveryMethod)->delivery_method_name;

        $addressId = optional($order->userCompanyAddress)->user_company_address_id;
        $address = optional($order->userCompanyAddress)->full_address;

        $freeDeliveryLimit = Setting::where('key', 'min_order_free_delivery')->value('value') ?? 0;
        $deliveryCost = '0';

        if ($order->product_total_amount < $freeDeliveryLimit) {
            $deliveryCost = DeliveryMethod::where('delivery_method_id', $deliveryId)->value('delivery_method_amount') ?? 0;
        }

        $items = $order->items->map(function($itm) {
            $variant = $itm->variant;
            $product = $variant->product;

            $rawOptions     = optional($variant->mvariantDetail)->options;
            $rawOptionValue = optional($variant->mvariantDetail)->option_value;

            $parsedOptions = null;
            if (is_string($rawOptions)) {
                $parsedOptions = json_decode($rawOptions, true);
            } elseif (is_array($rawOptions)) {
                $parsedOptions = $rawOptions;
            }

            $parsedOptionValue = null;
            if (is_string($rawOptionValue)) {
                $parsedOptionValue = json_decode($rawOptionValue, true);
            } elseif (is_array($rawOptionValue)) {
                $parsedOptionValue = $rawOptionValue;
            }

            return [
                'order_item_id' => $itm->order_item_id,
                'mvariant_id'   => $variant->mvariant_id,
                'quantity'      => $itm->quantity,
                'unit_price'    => (float) $itm->unit_price,

                'variant' => [
                    'sku'           => $variant->sku,
                    'image'         => $variant->mvariant_image,
                    'price'         => (float) $variant->price,
                    'compare_price' => (float) $variant->compare_price,
                    'cost_price'    => (float) $variant->cost_price,
                    'options'       => $parsedOptions,
                    'option_value'  => $parsedOptionValue,
                    'stock'         => optional($variant->mstock)->quantity ?? 0,
                    'mlocation_id'  => optional($variant->mstock)->mlocation_id,
                ],

                'product' => [
                    'mproduct_id'    => $product->mproduct_id,
                    'mproduct_title' => $product->mproduct_title,
                    'mproduct_slug'  => $product->mproduct_slug,
                    'mproduct_image' => $product->mproduct_image,
                ],
            ];
        })->values();

        $payload = [
            'order_id'=> $order->order_id,
            'user'    => [
                'id'    => $order->user->id,
                'name'  => $order->user->name,
                'email' => $order->user->email,
            ],
            'order_date'          => $order->created_at->toDateTimeString(),
            'units'               => $units,
            'payment_status'      => $order->status,
            'fulfillment_status'  => $order->fulfillment_status,
            'skus'                => $skus,

            'delivery' => [
                'method_id'  => $deliveryId, 
                'method'  => $deliveryName,
                'address_id' => $addressId,
                'address' => $address,
            ],

            'coupon' => $order->coupon ? [
                'coupon_id'      => $order->coupon->coupon_id,
                'code'           => $order->coupon->code,
                'discount_type'  => $order->coupon->discount_type,
                'discount_value' => $order->coupon->discount_value,
                'expires_at'     => $order->coupon->expires_at,
                'usage_limit'    => $order->coupon->usage_limit,
                'per_user_limit' => $order->coupon->per_user_limit,
                'min_cart_value' => $order->coupon->min_cart_value,
            ] : null,

            'delivery_instructions' => $order->delivery_instructions,

            'summary' => [
                'subtotal'        => $order->product_total_amount,
                'wallet_discount' => $walletDiscount, 
                'coupon_discount' => $couponDiscount, 
                'delivery_cost'   => $deliveryCost, 
                'vat'             => $order->vat, 
                'payment_total'   => $order->total_amount, 
                'total_paid'      => $order->total_paid, 
            ],

            'items' => $items,
        ];

        return response()->json([
            'status'  => true,
              'cdnURL'     => config('cdn.url'),
            'message' => 'Fetch Order Successfully',
            'order'   => $payload,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $validated = $request->validate([
            'status' => [
                'required',
                Rule::in(['pending', 'paid', 'shipped', 'cancelled']),
            ],
        ]);

        $order->status = $validated['status'];
        $order->save();

        return response()->json([
            'status'  => true,
            'cdnURL'     => config('cdn.url'),
            'message' => 'Order Status Updated Successfully',
            'order'    => $order->load(['items', 'user:id,name,email']),
        ], 200);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (! $order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();
        return response()->json(null, 204);
    }
}
