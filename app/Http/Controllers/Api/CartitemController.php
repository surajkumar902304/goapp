<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart_item;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Mvariant;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\Auth;

class CartitemController extends Controller
{
   public function index(Request $request)
    {
        try {
            $userId = auth()->id();

            $wishlistVariantIds = Wishlist::where('user_id', $userId)
                                                    ->pluck('mvariant_id')
                                                    ->toArray();

            $cartItems = Cart_item::where('user_id', $userId)
                                ->where('status', 'active')
                                ->get();

            $cartWithProduct = $cartItems->map(function ($item) use ($wishlistVariantIds) {
                $variant = Mvariant::with([
                    'product:mproduct_id,mproduct_title,mproduct_image,mproduct_slug,mproduct_desc,status,saleschannel,mproduct_type_id,mbrand_id',
                    'product.type:mproduct_type_id,mproduct_type_name',
                    'product.brand:mbrand_id,mbrand_name',
                    'mstock:mvariant_id,quantity,mlocation_id',
                    'mvariantDetail:mvariant_id,options,option_value'
                ])->find($item->mvariant_id);

                if (! $variant) {
                    return null; 
                }

                $product = $variant->product;

                $rawOptions      = optional($variant->mvariantDetail)->options;
                $rawOptionValue  = optional($variant->mvariantDetail)->option_value;

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

                $inWishlist = in_array($variant->mvariant_id, $wishlistVariantIds);

                return [
                    'cart_item_id' => $item->cart_item_id,
                    'mvariant_id'  => $variant->mvariant_id,
                    'quantity'     => $item->quantity,
                    'status'       => $item->status,

                    'product' => [
                        'mproduct_id'    => $product->mproduct_id,
                        'mproduct_title' => $product->mproduct_title,
                        'mproduct_image' => $product->mproduct_image,
                        'mproduct_slug'  => $product->mproduct_slug,
                        'mproduct_desc'  => $product->mproduct_desc,
                        'status'         => $product->status,
                        'saleschannel'   => $product->saleschannel,
                        'product_type'   => optional($product->type)->mproduct_type_name,
                        'brand_name'     => optional($product->brand)->mbrand_name,

                        'user_info_wishlist'  => $inWishlist,

                        'mvariant_id'    => $variant->mvariant_id,
                        'sku'            => $variant->sku,
                        'image'          => $variant->mvariant_image,
                        'price'          => $variant->price,
                        'compare_price'  => $variant->compare_price,
                        'cost_price'     => $variant->cost_price,
                        'taxable'        => $variant->taxable,
                        'barcode'        => $variant->barcode,
                        'options'        => $parsedOptions,
                        'option_value'   => $parsedOptionValue,
                        'stock'         => optional($variant->mstock)->quantity ?? 0,
                        'mlocation_id'  => optional($variant->mstock)->mlocation_id,
                    ],
                ];
            })
            ->filter() 
            ->values();

            return response()->json([
                'status'    => true,
                'message'   => 'Fetched all Cart Items successfully',
                'cdnURL'    => config('cdn.url'),
                'cart_item' => $cartWithProduct,
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(['status' => false, 'message' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => false, 'message' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'message' => 'Token not found'], 401);
        }
    }

    public function store(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.'], 404);
            }

            $request->validate([
                'cart' => 'nullable|array'
            ]);

            $incomingCart = collect($request->cart);

            if ($incomingCart->isEmpty()) {
                Cart_item::where('user_id', $user->id)->delete();

                return response()->json([
                    'status' => true,
                    'message' => 'Cart cleared successfully.'
                ], 200);
            }

            $request->validate([
                'cart.*.mvariant_id' => 'required|integer|distinct',
                'cart.*.quantity'    => 'required|integer|min:1'
            ]);

            $existingCart = Cart_item::where('user_id', $user->id)->get();
            $incomingProductIds = $incomingCart->pluck('mvariant_id')->all();
            $existingProductIds = $existingCart->pluck('mvariant_id')->all();

            $productsToDelete = array_diff($existingProductIds, $incomingProductIds);
            if (!empty($productsToDelete)) {
                Cart_item::where('user_id', $user->id)
                    ->whereIn('mvariant_id', $productsToDelete)
                    ->delete();
            }

            foreach ($incomingCart as $item) {
                $cartItem = $existingCart->firstWhere('mvariant_id', $item['mvariant_id']);

                if ($cartItem) {
                    if ($cartItem->quantity != $item['quantity']) {
                        $cartItem->quantity = $item['quantity'];
                        $cartItem->save();
                    }
                } else {
                    Cart_item::create([
                        'user_id'     => $user->id,
                        'mvariant_id' => $item['mvariant_id'],
                        'quantity'    => $item['quantity'],
                        'status'      => 'active'
                    ]);
                }
            }

            return response()->json([
                'status' => true,
                'message' => 'Cart updated successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error processing cart.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

}
