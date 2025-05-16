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
        try {
            $data = $request->validate([
                'user_id' => ['required','integer','exists:users,id'],
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();

            return response()->json([
                'status' => false,
                'message' => $errors->first(),
            ], 422);
        }
        
        
        $items = Wishlist::where('user_id', $data['user_id'])->pluck('mvariant_id');

        $brands = Mbrand::whereIn('mbrand_id', function ($query) use ($items) {
            $query->select('mproducts.mbrand_id')
                ->from('mvariants')
                ->join('mproducts', 'mvariants.mproduct_id', '=', 'mproducts.mproduct_id')
                ->whereIn('mvariants.mvariant_id', $items)
                ->whereNotNull('mproducts.mbrand_id');
        })
        ->distinct()
        ->get(['mbrand_id', 'mbrand_name', 'mbrand_image']);


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
