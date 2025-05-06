<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mbrand;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
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

        $mbrands = Mbrand::get();

        return response()->json([
            'status' => true,
            'message'     => 'Fetch all Brands Successfully',
            'cdnURL'     => config('cdn.url'),
            'mbrands' => $mbrands,
            'wishlistbrand' => $brands,
        ]);
    }

    
}
