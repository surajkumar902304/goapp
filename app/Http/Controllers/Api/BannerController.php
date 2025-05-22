<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Browsebanner;
use App\Models\HomeExploreDealBanner;
use App\Models\HomeFruitBanner;
use App\Models\HomeLargeBanner;
use App\Models\HomeSmallBanner;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function largeBanner()
    {
        $bigSliders = HomeLargeBanner::orderBy('home_large_banner_position')->get();

        return response()->json([
            'status' => true,
            'message'     => 'Fetch all Big Sliders Successfully',
            'cdnURL'     => config('cdn.url'),
            'bigSliders' => $bigSliders
        ]);
    }

    public function smallBanner()
    {
        $smallSliders = HomeSmallBanner::orderBy('home_small_banner_position')->get();

        return response()->json([
            'status' => true,
            'message'     => 'Fetch all Small Sliders Successfully',
            'cdnURL'     => config('cdn.url'),
            'smallSliders' => $smallSliders
        ]);
    }

    public function dealBanner()
    {
        $dealsSliders = HomeExploreDealBanner::orderBy('home_explore_deal_banner_position')->get();

        return response()->json([
            'status' => true,
            'message'     => 'Fetch all Deals Sliders Successfully',
            'cdnURL'     => config('cdn.url'),
            'dealsSliders' => $dealsSliders
        ]);
    }

    public function fruitBanner()
    {
        $fruitSliders = HomeFruitBanner::orderBy('home_fruit_banner_position')->get();

        return response()->json([
            'status' => true,
            'message'     => 'Fetch all Fruit Sliders Successfully',
            'cdnURL'     => config('cdn.url'),
            'fruitSliders' => $fruitSliders
        ]);
    }

    public function browseBanner()
    {
        $browseSliders = Browsebanner::orderBy('browsebanner_position')->get();

        return response()->json([
            'status' => true,
            'message'     => 'Fetch all Browse Sliders Successfully',
            'cdnURL'     => config('cdn.url'),
            'browseSliders' => $browseSliders
        ]);
    }
}
