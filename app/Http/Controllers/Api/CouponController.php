<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart_item;
use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Mvariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class CouponController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $coupons = Coupon::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->get();

        $updatedCoupons = $coupons->map(function ($coupon) use ($userId) {
            $isValid = $coupon->isValid();

            $hasUsageLeft = true;
            if ($coupon->usage_limit !== null && $coupon->totalUsed() >= $coupon->usage_limit) {
                $hasUsageLeft = false;
            }

            $userUsage = CouponUsage::where('coupon_id', $coupon->coupon_id)
                                    ->where('user_id', $userId)
                                    ->first();

            $userCanUse = true;
            if ($coupon->per_user_limit !== null && $userUsage && $userUsage->used_count >= $coupon->per_user_limit) {
                $userCanUse = false;
            }

            $coupon->can_be_applied = $isValid && $hasUsageLeft && $userCanUse;
            return $coupon;
        });

        return response()->json([
            'status'  => true,
            'message' => 'Coupons fetched successfully',
            'data'    => $updatedCoupons,
        ], 200);
    }

    public function show($coupon_id)
    {
        $userId = auth()->id();

        $coupon = Coupon::where('is_active', true)->find($coupon_id);

        if (! $coupon) {
            return response()->json(['status' => false, 'message' => 'Coupon not found or inactive'], 404);
        }

        $isValid = $coupon->isValid();

        $hasUsageLeft = true;
        if ($coupon->usage_limit !== null && $coupon->totalUsed() >= $coupon->usage_limit) {
            $hasUsageLeft = false;
        }

        $userUsage = CouponUsage::where('coupon_id', $coupon->coupon_id)
                                ->where('user_id', $userId)
                                ->first();

        $userCanUse = true;
        if ($coupon->per_user_limit !== null && $userUsage && $userUsage->used_count >= $coupon->per_user_limit) {
            $userCanUse = false;
        }

        $coupon->can_be_applied = $isValid && $hasUsageLeft && $userCanUse;

        return response()->json([
            'status'  => true,
            'message' => 'Coupon details fetched',
            'data'    => $coupon,
        ], 200);
    }

    public function applyCoupon(Request $request)
    {
        try {
            $userId = auth()->id();

            $data = $request->validate([
                'code' => ['required', 'string', 'exists:coupons,code'],
            ]);

            $code = strtoupper($data['code']);

            $coupon = Coupon::where('code', $code)->first();
            if (! $coupon) {
                return response()->json(['status'=>false,'message'=>'Coupon not found'], 404);
            }

            if (! $coupon->isValid()) {
                return response()->json(['status'=>false,'message'=>'Coupon is expired or inactive'], 400);
            }

            if ($coupon->usage_limit !== null && $coupon->totalUsed() >= $coupon->usage_limit) {
                return response()->json(['status'=>false,'message'=>'Coupon usage limit reached'], 400);
            }

            $usage = CouponUsage::where('coupon_id', $coupon->coupon_id)
                    ->where('user_id', $userId)
                    ->first();

            if ($coupon->per_user_limit !== null && $usage && $usage->used_count >= $coupon->per_user_limit) {
                return response()->json(['status'=>false,'message'=>'You have already used this coupon the maximum allowed times'], 400);
            }


            $cartItems = Cart_item::where('user_id', $userId)->where('status', 'active')->get();
            if ($cartItems->isEmpty()) {
                return response()->json(['status'=>false,'message'=>'Cart is empty'], 400);
            }

            $subtotal = 0;
            foreach ($cartItems as $item) {
                $variant = Mvariant::find($item->mvariant_id);
                if (! $variant) continue;

                $subtotal += ($variant->price * $item->quantity);
            }

            if ($subtotal < $coupon->min_cart_value) {
                return response()->json([
                    'status'  => false,
                    'message' => "Cart total must be at least {$coupon->min_cart_value} to apply this coupon"
                ], 400);
            }

            if ($coupon->discount_type === 'fixed') {
                $discountAmount = min($coupon->discount_value, $subtotal);
            } else {
                $discountAmount = ($coupon->discount_value / 100) * $subtotal;
            }

            $newTotal = $subtotal - $discountAmount;

            return response()->json([
                'status'         => true,
                'message'        => 'Coupon applied successfully',
                'original_total' => $subtotal,
                'discount'       => $discountAmount, 
                'new_total'      => $newTotal,
                'coupon_code'    => $coupon->code,
            ], 200);

        } catch (TokenExpiredException $e) {
            return response()->json(['status' => false, 'message' => 'Token expired'], 401);
        } catch (TokenInvalidException $e) {
            return response()->json(['status' => false, 'message' => 'Invalid token'], 401);
        } catch (JWTException $e) {
            return response()->json(['status' => false, 'message' => 'Token not found'], 401);
        }
    }
}
