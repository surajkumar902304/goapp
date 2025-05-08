<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart_item;
use App\Models\Mproduct;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CartitemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found from.',
                ], 404);
            }

            $cartItems = Cart_item::where('user_id', $user->id)
                        ->where('status', 'active')
                        ->get();

            $wishlistProductIds = [];

            $cartWithProduct = $cartItems->map(function ($item) use ($wishlistProductIds) {
                $product = Mproduct::with([
                    'type:mproduct_type_id,mproduct_type_name',
                    'brand:mbrand_id,mbrand_name',
                    'mvariantsApi' => fn($q) => $q
                        ->join('mvariant_details', 'mvariant_details.mvariant_id', '=', 'mvariants.mvariant_id')
                        ->join('mstocks', 'mstocks.mvariant_id', '=', 'mvariants.mvariant_id')
                        ->select(
                            'mvariants.*',
                            'mvariant_details.options',
                            'mvariant_details.option_value',
                            'mstocks.quantity',
                            'mstocks.mlocation_id'
                        )
                ])
                ->where('mproduct_id', $item->mproduct_id)
                ->first();

                if (!$product || $product->mvariantsApi->isEmpty()) {
                    $item->product = null;
                    return $item;
                }

                $variant = $product->mvariantsApi->first();

                $item->product = [
                    'mproduct_id'        => $product->mproduct_id,
                    'mproduct_title'     => $product->mproduct_title,
                    'mproduct_image'     => $product->mproduct_image,
                    'mproduct_slug'      => $product->mproduct_slug,
                    'mproduct_desc'      => $product->mproduct_desc,
                    'status'             => $product->status,
                    'saleschannel'       => $product->saleschannel,
                    'product_type'       => optional($product->type)->mproduct_type_name,
                    'brand_name'         => optional($product->brand)->mbrand_name,
                    'user_info_wishlist' => in_array($product->mproduct_id, $wishlistProductIds, true),

                    'mvariant_id'        => $variant->mvariant_id,
                    'sku'                => $variant->sku,
                    'image'              => $variant->mvariant_image,
                    'price'              => $variant->price,
                    'compare_price'      => $variant->compare_price,
                    'cost_price'         => $variant->cost_price,
                    'taxable'            => $variant->taxable,
                    'barcode'            => $variant->barcode,
                    'options'            => $variant->options,
                    'option_value'       => $variant->option_value,
                    'quantity'           => $variant->quantity,
                    'mlocation_id'       => $variant->mlocation_id,
                ];

                return $item;
            });

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


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            if (!$user) {
                return response()->json(['status' => false, 'message' => 'User not found.'], 404);
            }

            $request->validate([
                'cart' => 'required|array|min:1',
                'cart.*.mproduct_id' => 'required|integer|distinct',
                'cart.*.quantity'    => 'required|integer|min:1'
            ]);

            $incomingCart = collect($request->cart);
            $existingCart = Cart_item::where('user_id', $user->id)->get();

            $incomingProductIds = $incomingCart->pluck('mproduct_id')->all();
            $existingProductIds = $existingCart->pluck('mproduct_id')->all();

            $productsToDelete = array_diff($existingProductIds, $incomingProductIds);
            Cart_item::where('user_id', $user->id)
                ->whereIn('mproduct_id', $productsToDelete)
                ->delete();

            foreach ($incomingCart as $item) {
                $cartItem = $existingCart->firstWhere('mproduct_id', $item['mproduct_id']);
                if ($cartItem) {
                    if ($cartItem->quantity != $item['quantity']) {
                        $cartItem->quantity = $item['quantity'];
                        $cartItem->save();
                    }
                } else {
                    Cart_item::create([
                        'user_id'     => $user->id,
                        'mproduct_id' => $item['mproduct_id'],
                        'quantity'    => $item['quantity'],
                        'status'      => 'active'
                    ]);
                }
            }

            return response()->json([
                'status'  => true,
                'message' => 'Cart updated successfully.'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'status'  => false,
                'message' => 'Error processing cart.',
                'error'   => $e->getMessage()
            ], 500);
        }
    }
   
}
