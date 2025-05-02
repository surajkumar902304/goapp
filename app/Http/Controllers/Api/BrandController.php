<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Mbrand;
use Illuminate\Http\Request;

class BrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    // Without product get brands
        // $mbrands = Mbrand::whereNotNull('mbrand_image')
        // ->whereIn('mbrand_id', function($query) {
        //     $query->select('mbrand_id')->from('mproducts');
        // })
        // ->get();

    // with product and variants details get brands
        // $mbrands = Mbrand::with('mproducts.mvariants')
        // ->whereNotNull('mbrand_image')
        // ->whereIn('mbrand_id', function($query) {
        //     $query->select('mbrand_id')->from('mproducts');
        // })
        // ->get();

        $mbrands = Mbrand::with('mproducts')
        ->whereNotNull('mbrand_image')
        ->whereIn('mbrand_id', function($query) {
            $query->select('mbrand_id')->from('mproducts');
        })
        ->get();

        return response()->json([
            'status' => true,
            'message'     => 'Fetch all Brands Successfully',
            'cdnURL'     => 'https://cdn.truewebpro.com/',
            'mbrands' => $mbrands
        ]);
    }

    
}
