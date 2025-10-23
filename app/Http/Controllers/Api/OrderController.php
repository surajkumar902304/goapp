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
use App\Models\Payment;
use App\Models\Referral;
use App\Models\Setting;
use App\Models\UserTag;
use App\Models\UserTagPrice;
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

        $payload = $paginator->getCollection()->map(function ($order) {
            $units = $order->items->sum('quantity');
            $skus = $order->items->count();

            $walletDiscount = $order->wallet_discount ?? 0;
            $couponDiscount = $order->coupon_discount ?? 0;

            $delivery = $order->deliveryMethod;
            $address = $order->userCompanyAddress;

            $freeDeliveryLimit = Setting::where('key', 'min_order_free_delivery')->value('value') ?? 0;
            $deliveryCost = '0';

            if ($order->product_total_amount < $freeDeliveryLimit) {
                $deliveryCost = DeliveryMethod::where('delivery_method_id', $delivery->delivery_method_id)->value('delivery_method_amount') ?? 0;
            }

            $items = $order->items->map(function ($itm) {
                $variant = $itm->variant;
                $product = $variant->product;

                $opts = $variant->mvariantDetail->options ?? null;
                $vals = $variant->mvariantDetail->option_value ?? null;
                $opts = is_string($opts) ? json_decode($opts, true) : $opts;
                $vals = is_string($vals) ? json_decode($vals, true) : $vals;

                return [
                    'order_item_id' => $itm->order_item_id,
                    'mvariant_id' => $variant->mvariant_id,
                    'quantity' => $itm->quantity,
                    'unit_price' => (float) $itm->unit_price,
                    'variant' => [
                        'sku' => $variant->sku,
                        'image' => $variant->mvariant_image,
                        'price' => (float) $variant->price,
                        'compare_price' => (float) $variant->compare_price,
                        'cost_price' => (float) $variant->cost_price,
                        'options' => $opts,
                        'option_value' => $vals,
                    ],
                    'product' => [
                        'mproduct_id' => $product->mproduct_id,
                        'mproduct_title' => $product->mproduct_title,
                        'mproduct_slug' => $product->mproduct_slug,
                        'mproduct_image' => $product->mproduct_image,
                    ],
                ];
            });

            return [
                'order_id' => $order->order_id,
                'user' => [
                    'id' => $order->user->id,
                    'name' => $order->user->name,
                    'email' => $order->user->email,
                ],
                'order_date' => $order->created_at->toDateTimeString(),
                'units' => $units,
                'payment_status' => $order->status,
                'fulfillment_status' => $order->fulfillment_status,
                'skus' => $skus,
                'delivery' => [
                    'method_id' => $delivery->delivery_method_id ?? null,
                    'method' => $delivery->delivery_method_name ?? null,
                    'address_id' => $address->user_company_address_id ?? null,
                    'address' => $address->full_address ?? null,
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
            ];
        });

        $paginator->setCollection($payload);

        return response()->json([
            'status' => true,
            'message' => 'Fetch all Orders Successfully',
            'cdnURL' => config('cdn.url'),
            'orders' => $paginator->items(),
            'meta' => [
                'current_page' => $paginator->currentPage(),
                'per_page' => $paginator->perPage(),
                'total' => $paginator->total(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ], 200);
    }

    public function store(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'wallet_discount' => ['nullable', 'numeric'],
            'coupon_discount' => ['nullable', 'numeric'],
            'user_company_address_id' => ['required', 'integer', 'exists:user_company_addresses,user_company_address_id'],
            'delivery_method_id' => ['required', 'integer', 'exists:delivery_methods,delivery_method_id'],
            'delivery_instructions' => ['nullable', 'string'],
            'coupon_id' => ['nullable', 'string', 'regex:/^\d+$/'],
            'pay_by_bank' => ['required', 'boolean'],
            'payment_status' => ['nullable', 'string'],
            'payment_provider' => ['nullable', 'string'],
            'payment_reference' => ['nullable', 'string'],

            'payment_intent_id' => ['nullable', 'string'],
            'payment_method_id' => ['nullable', 'string'],
            'customer_id' => ['nullable', 'string'],
            'currency' => ['nullable', 'string'],
            'amount' => ['nullable', 'integer'],
            'status' => ['nullable', 'string'],
            'receipt_email' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'metadata' => ['nullable', 'string'],
            'raw_payload' => ['nullable', 'string'],
        ]);

        $couponId = trim($validated['coupon_id'] ?? '') ?: null;

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

            $tagType = null;
            $percent = null;
            $tagPriceMap = collect();

            if ($user && $user->user_tag_id) {
                $tag = UserTag::where('user_tag_id', $user->user_tag_id)
                    ->where('is_active', 1)
                    ->first(['user_tag_id', 'type', 'discount']);

                if ($tag) {
                    $t = strtolower($tag->type ?? '');
                    if ($t === 'custom') {
                        $tagType = 'custom';
                        $variantIdsInCart = $cartItems->pluck('mvariant_id')->all();

                        $tagPriceMap = UserTagPrice::where('user_tag_id', $tag->user_tag_id)
                            ->whereIn('mvariant_id', $variantIdsInCart)
                            ->pluck('tag_price', 'mvariant_id');

                    } elseif ($t === 'percentage') {
                        $tagType = 'percentage';
                        $raw = (float) ($tag->discount ?? 0);
                        $percent = max(0.0, min(100.0, $raw));
                    }
                }
            }

            $vatPercent = DB::table('product_vats')->value('product_vat') ?? 20;
            $vatPercent = $vatPercent / 100;

            $productTotal = 0.0;
            $totalvat = 0.0;
            $effectiveUnit = [];

            foreach ($cartItems as $cart) {
                $quantity = (int) $cart->quantity;
                $basePrice = (float) $cart->mvariant->price;

                $eff = $basePrice;
                if ($tagType === 'custom') {
                    if (isset($tagPriceMap[$cart->mvariant_id])) {
                        $eff = (float) $tagPriceMap[$cart->mvariant_id];
                    }
                } elseif ($tagType === 'percentage' && $percent !== null) {
                    $eff = round($basePrice * (1 - $percent / 100), 2);
                    if ($eff < 0)
                        $eff = 0.0;
                }

                $effectiveUnit[$cart->mvariant_id] = $eff;

                $vatAmountPerUnit = 0.0;
                if ((int) $cart->mvariant->taxable === 1) {
                    $vatAmountPerUnit = $eff * $vatPercent;
                }

                $productTotal += $eff * $quantity;
                $totalvat += $vatAmountPerUnit * $quantity;
            }

            $freeDeliveryLimit = (float) (Setting::where('key', 'min_order_free_delivery')->value('value') ?? 0);
            $deliveryCharge = 0.0;

            if ($productTotal < $freeDeliveryLimit) {
                $deliveryCharge = (float) (DeliveryMethod::where('delivery_method_id', $validated['delivery_method_id'])
                    ->value('delivery_method_amount') ?? 0);
            }

            $wallet_discount = (float) ($validated['wallet_discount'] ?? 0);
            $coupon_discount = (float) ($validated['coupon_discount'] ?? 0);

            $grossTotal = $productTotal + $totalvat + $deliveryCharge;
            $amountBeforeWallet = max(0, $grossTotal - $coupon_discount);
            $amountDueAfterWallet = max(0, $amountBeforeWallet - $wallet_discount);

            $wallet = Wallet::lockForUpdate()->firstOrCreate(
                ['user_id' => $user->id],
                ['balance' => 0]
            );

            if ($wallet_discount > $wallet->balance) {
                DB::rollBack();
                return response()->json(['status' => false, 'message' => 'Insufficient wallet balance'], 400);
            }

            if ($wallet_discount > 0) {
                $wallet->decrement('balance', $wallet_discount);

                WalletTransaction::create([
                    'wallet_id' => $wallet->wallet_id,
                    'type' => 'debit',
                    'amount' => $wallet_discount,
                    'reference' => 'ORDER-' . uniqid(),
                    'description' => 'Wallet used during checkout',
                ]);
            }

            $payByBank = $request->boolean('pay_by_bank');

            if ($amountDueAfterWallet <= 0.00001) {
                $status = 'paid';
                $creditPayByBankBonus = false;

            } elseif ($payByBank) {
                $status = 'pending';
                $creditPayByBankBonus = true;

            } else {
                $status = 'paid';
                $creditPayByBankBonus = false;
            }

            if ($creditPayByBankBonus) {
                $wallet->increment('balance', 1);

                WalletTransaction::create([
                    'wallet_id' => $wallet->wallet_id,
                    'type' => 'credit',
                    'amount' => 1,
                    'reference' => 'PAYBYBANK-' . uniqid(),
                    'description' => 'Pay by bank bonus',
                ]);
            }

            $finalTotal = $amountDueAfterWallet;

            $order = Order::create([
                'user_id' => $user->id,
                'total_amount' => $grossTotal,
                'wallet_discount' => $wallet_discount,
                'coupon_discount' => $coupon_discount,
                'status' => $status,
                'fulfillment_status' => 'unfulfilled',
                'user_company_address_id' => $validated['user_company_address_id'],
                'delivery_method_id' => $validated['delivery_method_id'],
                'vat' => $totalvat,
                'total_paid' => $finalTotal,
                'product_total_amount' => $productTotal,
                'delivery_instructions' => $validated['delivery_instructions'] ?? null,
                'coupon_id' => $couponId,
                'pay_by_bank' => $payByBank,
                'payment_status' => $validated['payment_status'] ?? null,
                'payment_provider' => $validated['payment_provider'] ?? null,
                'payment_reference' => $validated['payment_reference'] ?? null,
            ]);

            if (!$payByBank) {
                Payment::create([
                    'order_id' => $order->order_id,
                    'user_id' => $user->id,
                    'provider' => 'stripe',

                    'payment_intent_id' => $validated['payment_intent_id'],
                    'payment_method_id' => $validated['payment_method_id'] ?? null,
                    'customer_id' => $validated['customer_id'] ?? null,
                    'currency' => $validated['currency'],
                    'amount' => $validated['amount'], 
                    'status' => $validated['status'], 
                    'receipt_email' => $validated['receipt_email'] ?? null,
                    'description' => $validated['description'] ?? null,
                    'metadata' => $validated['metadata'] ?? null,
                    'raw_payload' => $validated['raw_payload'] ?? null,
                ]);
            }

            foreach ($cartItems as $cart) {
                $quantity = (int) $cart->quantity;
                $unit_price = $effectiveUnit[$cart->mvariant_id] ?? (float) $cart->mvariant->price;

                OrderItem::create([
                    'order_id' => $order->order_id,
                    'mvariant_id' => $cart->mvariant_id,
                    'quantity' => $quantity,
                    'unit_price' => $unit_price,
                ]);

                Mstock::where('mvariant_id', $cart->mvariant_id)
                    ->decrement('quantity', $quantity);
            }

            Cart_item::where('user_id', $user->id)->delete();

            if ($user->rep_id) {
                $rep_id = $user->rep_id;

                $commissionPercent = (float) (Customer::where('rep_id', $rep_id)->value('commission_percent') ?? 0);
                $commissionAmount = ($productTotal * $commissionPercent) / 100;

                OrderCommission::create([
                    'order_id' => $order->order_id,
                    'rep_id' => $rep_id,
                    'user_id' => $user->id,
                    'product_total' => $productTotal,
                    'commission_percent' => $commissionPercent,
                    'commission_amount' => $commissionAmount,
                ]);

                CustomerCommission::updateOrCreate(
                    ['rep_id' => $rep_id],
                    ['total_commission' => DB::raw("total_commission + {$commissionAmount}")]
                );
            }

            if (!is_null($couponId)) {
                CouponUsage::updateOrCreate(
                    ['coupon_id' => $couponId, 'user_id' => $user->id],
                    ['used_count' => DB::raw('used_count + 1')]
                );
            }

            $hasPlacedOrdersBefore = Order::where('user_id', $user->id)
                ->where('order_id', '!=', $order->order_id)
                ->exists();

            $referral = Referral::where('user_id', $user->id)
                ->where('has_received_bonus', 0)
                ->first();

            if (!$hasPlacedOrdersBefore && $referral) {
                $referrerId = $referral->referrer_id;

                $userWallet = Wallet::firstOrCreate(
                    ['user_id' => $user->id],
                    ['balance' => 0]
                );
                $userWallet->increment('balance', 10);

                WalletTransaction::create([
                    'wallet_id' => $userWallet->wallet_id,
                    'type' => 'credit',
                    'amount' => 10,
                    'reference' => 'REFBONUS-' . uniqid(),
                    'description' => 'Referral bonus for joining (first order)',
                ]);

                $referrerWallet = Wallet::firstOrCreate(
                    ['user_id' => $referrerId],
                    ['balance' => 0]
                );
                $referrerWallet->increment('balance', 10);

                WalletTransaction::create([
                    'wallet_id' => $referrerWallet->wallet_id,
                    'type' => 'credit',
                    'amount' => 10,
                    'reference' => 'REFBONUS-' . uniqid(),
                    'description' => 'Referral bonus for referring (first order)',
                ]);

                $referral->has_received_bonus = 1;
                $referral->save();
            }

            DB::commit();

            $order->load([
                'items.variant.product',
                'user:id,name,email',
                'userCompanyAddress',
                'deliveryMethod',
                'coupon'
            ]);

            Mail::to($order->user->email)->send(new OrderPlacedMail($order));

            return response()->json([
                'status' => true,
                'message' => 'Order Placed Successfully',
                'cdnURL' => config('cdn.url'),
                'order' => $order,
            ], 201);

        } catch (\Throwable $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed to create order.',
                'error' => $e->getMessage(),
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

        if (!$order) {
            return response()->json(['status' => false, 'message' => 'Order not found'], 404);
        }

        $units = $order->items->sum('quantity');
        $skus = $order->items->count();

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

        $items = $order->items->map(function ($itm) {
            $variant = $itm->variant;
            $product = $variant->product;

            $rawOptions = optional($variant->mvariantDetail)->options;
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
                'mvariant_id' => $variant->mvariant_id,
                'quantity' => $itm->quantity,
                'unit_price' => (float) $itm->unit_price,

                'variant' => [
                    'sku' => $variant->sku,
                    'image' => $variant->mvariant_image,
                    'price' => (float) $variant->price,
                    'compare_price' => (float) $variant->compare_price,
                    'cost_price' => (float) $variant->cost_price,
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

        $payload = [
            'order_id' => $order->order_id,
            'user' => [
                'id' => $order->user->id,
                'name' => $order->user->name,
                'email' => $order->user->email,
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
        ];

        return response()->json([
            'status' => true,
            'cdnURL' => config('cdn.url'),
            'message' => 'Fetch Order Successfully',
            'order' => $payload,
        ], 200);
    }

    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if (!$order) {
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
            'status' => true,
            'cdnURL' => config('cdn.url'),
            'message' => 'Order Status Updated Successfully',
            'order' => $order->load(['items', 'user:id,name,email']),
        ], 200);
    }

    public function destroy($id)
    {
        $order = Order::find($id);
        if (!$order) {
            return response()->json(['message' => 'Order not found'], 404);
        }

        $order->delete();
        return response()->json(null, 204);
    }
}
