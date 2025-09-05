<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Browsebanner;
use App\Models\HomeExploreDealBanner;
use App\Models\HomeFruitBanner;
use App\Models\HomeLargeBanner;
use App\Models\HomeRoundBanner;
use App\Models\HomeSmallBanner;
use App\Models\LoyaltyRewardBanner;
use App\Models\Mtag;
use App\Models\Mvariant;
use App\Models\NewProduct;
use App\Models\SliderHeader;
use App\Models\TopSeller;
use App\Models\Wishlist;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function homeBanner()
    {
        $roundSliders = HomeRoundBanner::orderBy('home_round_banner_position')->get()->makeHidden(['created_at', 'updated_at']);
        $bigSliders   = HomeLargeBanner::orderBy('home_large_banner_position')->get()->makeHidden(['created_at', 'updated_at']);
        $smallSliders = HomeSmallBanner::orderBy('home_small_banner_position')->get()->makeHidden(['created_at', 'updated_at']);
        $dealsSliders = HomeExploreDealBanner::orderBy('home_explore_deal_banner_position')->get()->makeHidden(['created_at', 'updated_at']);
        $fruitSliders = HomeFruitBanner::orderBy('home_fruit_banner_position')->get()->makeHidden(['created_at', 'updated_at']);

        $dealHeader = SliderHeader::where('header_name', 'first banner slider')->value('header_value');
        $fruitHeader = SliderHeader::where('header_name', 'second banner slider')->value('header_value');

        return response()->json([
            'status'        => true,
            'message'       => 'Fetch all Sliders Successfully',
            'cdnURL'        => config('cdn.url'),
            'roundSliders'  => $roundSliders,
            'bigSliders'    => $bigSliders,
            'smallSliders'  => $smallSliders,
            'dealsHeader'   => $dealHeader,
            'dealsSliders'  => $dealsSliders,
            'fruitHeader'   => $fruitHeader,
            'fruitSliders'  => $fruitSliders,
        ]);
    }

    public function roundBanner()
    {
        $roundSliders = HomeRoundBanner::orderBy('home_round_banner_position')->get();

        return response()->json([
            'status'     => true,
            'message'    => 'Fetch all Round Sliders Successfully',
            'cdnURL'     => config('cdn.url'),
            'roundSliders' => $roundSliders
        ]);
    }

    public function largeBanner()
    {
        $bigSliders = HomeLargeBanner::orderBy('home_large_banner_position')->get();

        return response()->json([
            'status'     => true,
            'message'    => 'Fetch all Big Sliders Successfully',
            'cdnURL'     => config('cdn.url'),
            'bigSliders' => $bigSliders
        ]);
    }

    public function smallBanner()
    {
        $smallSliders = HomeSmallBanner::orderBy('home_small_banner_position')->get();

        return response()->json([
            'status'        => true,
            'message'       => 'Fetch all Small Sliders Successfully',
            'cdnURL'        => config('cdn.url'),
            'smallSliders'  => $smallSliders
        ]);
    }

    public function dealBanner()
    {
        $dealsSliders = HomeExploreDealBanner::orderBy('home_explore_deal_banner_position')->get();

        $sliderHeader = SliderHeader::where('header_name', 'first banner slider')->first();

        $header = $sliderHeader && $sliderHeader->header_value
            ? $sliderHeader->header_value
            : null;

        return response()->json([
            'status'        => true,
            'message'       => 'Fetch all Deals Sliders Successfully',
            'cdnURL'        => config('cdn.url'),
            'slider_header' => $header,
            'dealsSliders'  => $dealsSliders
        ]);
    }

    public function fruitBanner()
    {
        $fruitSliders = HomeFruitBanner::orderBy('home_fruit_banner_position')->get();

        $sliderHeader = SliderHeader::where('header_name', 'second banner slider')->first();

        $header = $sliderHeader && $sliderHeader->header_value
            ? $sliderHeader->header_value
            : null;

        return response()->json([
            'status'        => true,
            'message'       => 'Fetch all Fruit Sliders Successfully',
            'cdnURL'        => config('cdn.url'),
            'slider_header' => $header,
            'fruitSliders'  => $fruitSliders
        ]);
    }

    public function browseBanner()
    {
        $browseSliders = Browsebanner::orderBy('browsebanner_position')->get();

        return response()->json([
            'status'        => true,
            'message'       => 'Fetch all Browse Sliders Successfully',
            'cdnURL'        => config('cdn.url'),
            'browseBanners' => $browseSliders
        ]);
    }

    public function productBanners()
    {
        $uid = auth()->id();
        $allTags = Mtag::pluck('mtag_name', 'mtag_id');

        $transformVariants = function ($sliderMap, $sliderKeyName) use ($allTags, $uid) {
            $variantIds = $sliderMap->keys();

            $variants = Mvariant::whereIn('mvariant_id', $variantIds)
                ->select('mvariant_id', 'sku', 'mvariant_image', 'price', 'compare_price', 'cost_price', 'taxable', 'barcode', 'mproduct_id')
                ->with([
                    'product' => function ($q) {
                        $q->select('mproduct_id', 'mproduct_title', 'mproduct_image', 'mproduct_slug', 'mproduct_desc', 'status', 'saleschannel', 'mbrand_id', 'mproduct_type_id', 'mtags')
                            ->where('status', 'Active')
                            ->whereJsonContains('saleschannel', 'Online Store')
                            ->with([
                                'brand:mbrand_id,mbrand_name',
                                'type:mproduct_type_id,mproduct_type_name'
                            ]);
                    },
                    'details:mvariant_detail_id,mvariant_id,options,option_value',
                    'mstock:mstock_id,mvariant_id,quantity,mlocation_id',
                    'productoffer:product_offer_id,mvariant_id,product_deal_tag,product_offer',
                ])
                ->get();

            return $variants->filter(fn($v) => $v->product)
                ->map(function ($v) use ($sliderMap, $sliderKeyName, $allTags, $uid) {
                    $optVals = collect($v->details)->reduce(fn($carry, $d) => array_merge($carry, (array)$d->option_value), []);
                    $optKeys = collect($v->details)->flatMap(fn($d) => $d->options)->unique()->values();
                    $p       = $v->product;
                    $brand   = $p?->brand;
                    $type    = $p?->type;

                    $inWishlist = Wishlist::where([
                        ['mvariant_id', '=', $v->mvariant_id],
                        ['user_id', '=', $uid],
                    ])->exists();

                    return [
                        $sliderKeyName => $sliderMap[$v->mvariant_id] ?? null,
                        'mvariant_id'  => $v->mvariant_id,
                        'product' => [
                            'mproduct_id'       => $p?->mproduct_id,
                            'mproduct_title'    => $p?->mproduct_title,
                            'mproduct_image'    => $p?->mproduct_image,
                            'mproduct_slug'     => $p?->mproduct_slug,
                            'mproduct_desc'     => $p?->mproduct_desc,
                            'status'            => $p?->status,
                            'saleschannel'      => $p?->saleschannel,
                            'brand_id'          => $brand?->mbrand_id,
                            'brand_name'        => $brand?->mbrand_name,
                            'type_id'           => $type?->mproduct_type_id,
                            'product_type'      => $type?->mproduct_type_name,
                            'tag_ids'           => $p->mtags ?? [],
                            'tag_names'         => collect($p->mtags ?? [])->map(fn($id) => $allTags[$id] ?? null)->filter()->values()->toArray(),
                            'mvariant_id'       => $v->mvariant_id,
                            'sku'               => $v->sku,
                            'image'             => $v->mvariant_image,
                            'price'             => $v->price,
                            'quantity'          => $v->mstock?->quantity ?? 0,
                            'compare_price'     => $v->compare_price,
                            'cost_price'        => $v->cost_price,
                            'taxable'           => $v->taxable,
                            'barcode'           => $v->barcode,
                            'options'           => $optKeys,
                            'option_value'      => (object)$optVals,
                            'mlocation_id'      => $v->mstock?->mlocation_id,
                            'product_deal_tag'  => optional($v->productoffer)->product_deal_tag,
                            'product_offer'     => optional($v->productoffer)->product_offer,
                            'user_info_wishlist'=> $inWishlist,
                        ],
                    ];
                })->sortBy($sliderKeyName)->values();
        };

        $newProductMap = NewProduct::orderBy('new_product_id')->limit(20)->pluck('new_product_id', 'mvariant_id');
        $newProductHeader = SliderHeader::where('header_name', 'first product slider')->value('header_value');
        $newProductBanners = $transformVariants($newProductMap, 'new_product_id')->take(10);

        $topSellerMap = TopSeller::orderBy('top_seller_id')->limit(20)->pluck('top_seller_id', 'mvariant_id');
        $topSellerHeader = SliderHeader::where('header_name', 'second product slider')->value('header_value');
        $topSellerBanners = $transformVariants($topSellerMap, 'top_seller_id')->take(10);

        return response()->json([
            'status'             => true,
            'message'            => 'Fetched product sliders successfully.',
            'cdnURL'             => config('cdn.url'),
            'newProductHeader'   => $newProductHeader,
            'newProductBanners'  => $newProductBanners,
            'topSellerHeader'    => $topSellerHeader,
            'topSellerBanners'   => $topSellerBanners,
        ]);
    }

    public function newProductBanner()
    {
        $sliderMap = NewProduct::orderBy('new_product_id')
            ->limit(20)
            ->pluck('new_product_id', 'mvariant_id');

        $variantIds = $sliderMap->keys();

        $variants = Mvariant::whereIn('mvariant_id', $variantIds)
            ->select('mvariant_id', 'sku', 'mvariant_image', 'price', 'compare_price', 'cost_price', 'taxable', 'barcode', 'mproduct_id')
            ->with([
                'product' => function ($q) {
                    $q->select('mproduct_id', 'mproduct_title', 'mproduct_image', 'mproduct_slug', 'mproduct_desc', 'status', 'saleschannel', 'mbrand_id', 'mproduct_type_id', 'mtags')
                        ->where('status', 'Active')
                        ->whereJsonContains('saleschannel', 'Online Store')
                        ->with([
                            'brand:mbrand_id,mbrand_name',
                            'type:mproduct_type_id,mproduct_type_name'
                        ]);
                },
                'details:mvariant_detail_id,mvariant_id,options,option_value',
                'mstock:mstock_id,mvariant_id,quantity,mlocation_id',
                'productoffer:product_offer_id,mvariant_id,product_deal_tag,product_offer',
            ])
            ->get();

        $allTags = Mtag::pluck('mtag_name', 'mtag_id');
        $uid     = auth()->id();

        $payload = $variants->filter(fn($v) => $v->product) 
            ->map(function ($v) use ($sliderMap, $allTags, $uid) {
                $optVals = collect($v->details)->reduce(
                    fn($carry, $d) => array_merge($carry, (array)$d->option_value), []
                );

                $optKeys = collect($v->details)
                    ->flatMap(fn($d) => $d->options)
                    ->unique()
                    ->values();

                $p     = $v->product;
                $brand = $p?->brand;
                $type  = $p?->type;

                $inWishlist = Wishlist::where([
                    ['mvariant_id', '=', $v->mvariant_id],
                    ['user_id', '=', $uid],
                ])->exists();

                return [
                    'new_product_id' => $sliderMap[$v->mvariant_id] ?? null,
                    'mvariant_id'    => $v->mvariant_id,
                    'product' => [
                        'mproduct_id'       => $p?->mproduct_id,
                        'mproduct_title'    => $p?->mproduct_title,
                        'mproduct_image'    => $p?->mproduct_image,
                        'mproduct_slug'     => $p?->mproduct_slug,
                        'mproduct_desc'     => $p?->mproduct_desc,
                        'status'            => $p?->status,
                        'saleschannel'      => $p?->saleschannel,
                        'brand_id'          => $brand?->mbrand_id,
                        'brand_name'        => $brand?->mbrand_name,
                        'type_id'           => $type?->mproduct_type_id,
                        'product_type'      => $type?->mproduct_type_name,
                        'tag_ids'           => $p->mtags ?? [],
                        'tag_names'         => collect($p->mtags ?? [])->map(fn($id) => $allTags[$id] ?? null)->filter()->values()->toArray(),
                        'mvariant_id'       => $v->mvariant_id,
                        'sku'               => $v->sku,
                        'image'             => $v->mvariant_image,
                        'price'             => $v->price,
                        'quantity'          => $v->mstock?->quantity ?? 0,
                        'compare_price'     => $v->compare_price,
                        'cost_price'        => $v->cost_price,
                        'taxable'           => $v->taxable,
                        'barcode'           => $v->barcode,
                        'options'           => $optKeys,
                        'option_value'      => (object)$optVals,
                        'mlocation_id'      => $v->mstock?->mlocation_id,
                        'product_deal_tag'  => optional($v->productoffer)->product_deal_tag,
                        'product_offer'     => optional($v->productoffer)->product_offer,
                        'user_info_wishlist'=> $inWishlist,
                    ],
                ];
            })
            ->sortBy('new_product_id')
            ->values();

        $sliderHeader = SliderHeader::where('header_name', 'first product slider')->first();

        $header = $sliderHeader && $sliderHeader->header_value
            ? $sliderHeader->header_value
            : null;

        return response()->json([
            'status'            => true,
            'message'           => 'Fetch all New product Sliders Successfully',
            'cdnURL'            => config('cdn.url'),
            'slider_header'     => $header,
            'newProductBanners' => $payload,
        ]);
    }

    public function topSellerBanner()
    {
        $sliderMap = TopSeller::orderBy('top_seller_id')
            ->limit(20)
            ->pluck('top_seller_id', 'mvariant_id');

        $variantIds = $sliderMap->keys();

        $variants = Mvariant::whereIn('mvariant_id', $variantIds)
            ->select('mvariant_id', 'sku', 'mvariant_image', 'price', 'compare_price', 'cost_price', 'taxable', 'barcode', 'mproduct_id')
            ->with([
                'product' => function ($q) {
                    $q->select('mproduct_id', 'mproduct_title', 'mproduct_image', 'mproduct_slug', 'mproduct_desc', 'status', 'saleschannel', 'mbrand_id', 'mproduct_type_id', 'mtags')
                        ->where('status', 'Active')
                        ->whereJsonContains('saleschannel', 'Online Store')
                        ->with([
                            'brand:mbrand_id,mbrand_name',
                            'type:mproduct_type_id,mproduct_type_name'
                        ]);
                },
                'details:mvariant_detail_id,mvariant_id,options,option_value',
                'mstock:mstock_id,mvariant_id,quantity,mlocation_id',
                'productoffer:product_offer_id,mvariant_id,product_deal_tag,product_offer',
            ])
            ->get();

        $allTags = Mtag::pluck('mtag_name', 'mtag_id');
        $uid     = auth()->id();

        $payload = $variants->filter(fn($v) => $v->product)
            ->map(function ($v) use ($sliderMap, $allTags, $uid) {
                $optVals = collect($v->details)->reduce(
                    fn($carry, $d) => array_merge($carry, (array)$d->option_value), []
                );

                $optKeys = collect($v->details)
                    ->flatMap(fn($d) => $d->options)
                    ->unique()
                    ->values();

                $p     = $v->product;
                $brand = $p?->brand;
                $type  = $p?->type;

                $inWishlist = Wishlist::where([
                    ['mvariant_id', '=', $v->mvariant_id],
                    ['user_id', '=', $uid],
                ])->exists();

                return [
                    'top_seller_id' => $sliderMap[$v->mvariant_id] ?? null,
                    'mvariant_id' => $v->mvariant_id,
                    'product' => [
                        'mproduct_id'       => $p?->mproduct_id,
                        'mproduct_title'    => $p?->mproduct_title,
                        'mproduct_image'    => $p?->mproduct_image,
                        'mproduct_slug'     => $p?->mproduct_slug,
                        'mproduct_desc'     => $p?->mproduct_desc,
                        'status'            => $p?->status,
                        'saleschannel'      => $p?->saleschannel,
                        'brand_id'          => $brand?->mbrand_id,
                        'brand_name'        => $brand?->mbrand_name,
                        'type_id'           => $type?->mproduct_type_id,
                        'product_type'      => $type?->mproduct_type_name,
                        'tag_ids'           => $p->mtags ?? [],
                        'tag_names'         => collect($p->mtags ?? [])->map(fn($id) => $allTags[$id] ?? null)->filter()->values()->toArray(),
                        'mvariant_id'       => $v->mvariant_id,
                        'sku'               => $v->sku,
                        'image'             => $v->mvariant_image,
                        'price'             => $v->price,
                        'quantity'          => $v->mstock?->quantity ?? 0,
                        'compare_price'     => $v->compare_price,
                        'cost_price'        => $v->cost_price,
                        'taxable'           => $v->taxable,
                        'barcode'           => $v->barcode,
                        'options'           => $optKeys,
                        'option_value'      => (object)$optVals,
                        'mlocation_id'      => $v->mstock?->mlocation_id,
                        'product_deal_tag'  => optional($v->productoffer)->product_deal_tag,
                        'product_offer'     => optional($v->productoffer)->product_offer,
                        'user_info_wishlist'=> $inWishlist,
                    ],
                ];
            })
            ->sortBy('top_seller_id')
            ->values();

        $sliderHeader = SliderHeader::where('header_name', 'second product slider')->first();

        $header = $sliderHeader && $sliderHeader->header_value
            ? $sliderHeader->header_value
            : null;

        return response()->json([
            'status'            => true,
            'message'           => 'Fetch all Top seller Sliders Successfully',
            'cdnURL'            => config('cdn.url'),
            'slider_header'     => $header,
            'topSellerBanners'  => $payload,
        ]);
    }

    public function loyaltyRewardBanner()
    {
        $loyaltyRewardBanner = LoyaltyRewardBanner::get();

        return response()->json([
            'status'        => true,
            'message'       => 'Fetch Loyalty Reward Banner Successfully',
            'cdnURL'        => config('cdn.url'),
            'loyaltyRewardBanner'  => $loyaltyRewardBanner
        ]);
    }

}
