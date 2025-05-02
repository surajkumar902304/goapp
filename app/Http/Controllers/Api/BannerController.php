<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Browsebanner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function browseBanner()
    {
        $browseBanners = Browsebanner::orderBy('browsebanner_position')->get();

        return response()->json([
            'status' => true,
            'message'     => 'Fetch all Browse Banners Successfully',
            'cdnURL'     => 'https://cdn.truewebpro.com/',
            'browseBanners' => $browseBanners
        ]);
    }
}
