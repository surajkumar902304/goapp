<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Wishlist;
use App\Models\Mcategory;
use App\Models\Mcollection_auto;
use App\Models\Mproduct;

class WishlistController extends Controller
{
    /**
     * GET /api/wishlist?user_id={id}
     * List all wishlist entries for a user.
     */
    public function index(Request $request)
    {
        $data = $request->validate([
            'user_id' => ['required','integer','exists:users,id'],
        ]);

        $items = Wishlist::where('user_id', $data['user_id'])
            ->get();

            $brands = $items
            ->pluck('product.brand')
            ->filter()                   
            ->unique('mbrand_id')       
            ->values()                  
            ->map(fn($brand) => [      
                'mbrand_id'   => $brand->mbrand_id,
                'mbrand_name' => $brand->mbrand_name,
                'mbrand_image' => $brand->mbrand_image,
            ]);

        return response()->json([
            'status'   => true,
            'message'    => 'Fetch all Wishlist Successfully',
            'cdnURL'     => config('cdn.url'),
            'wishlist' => $items,
            'brand'    => $brands,
        ],200);
    }

    /**
     * POST /api/wishlist
     * Toggle a wishlist entry on/off for a user & product.
     *
     * Body: { user_id: ..., mproduct_id: ... }
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id'    => ['required','integer','exists:users,id'],
            'mproduct_id' => ['required','integer','exists:mproducts,mproduct_id'],
        ]);

        $entry = Wishlist::where('user_id', $data['user_id'])
            ->where('mproduct_id', $data['mproduct_id'])
            ->first();

        if ($entry) {
            $entry->delete();
            return response()->json([
                'status'  => true,
                'message' => 'Removed from wishlist',
            ],200);
        }

        $new = Wishlist::create($data);

        return response()->json([
            'status'  => true,
            'message' => 'Added to wishlist',
            'item'    => $new,
        ], 200);
    }
    
}
