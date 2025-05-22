<?php

namespace App\Http\Controllers;

use App\Models\HomeExploreDealBanner;
use App\Models\HomeFruitBanner;
use App\Models\HomeLargeBanner;
use App\Models\HomeSmallBanner;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class HomebannerController extends Controller
{

// Home Large Banner
    public function largeBannerList()
    {
       return view('admin.banner.home_large_banner');
    }

    public function largeBannerVlist()
    {
        $banners = HomeLargeBanner::with([
            'category:mcat_id,mcat_name',
            'subcategory:msubcat_id,msubcat_name',
            'product:mproduct_id,mproduct_title'
        ])
        ->orderBy('home_large_banner_position')
        ->get()
        ->map(function ($b) {
            return [
                'home_large_banner_id'      => $b->home_large_banner_id,
                'home_large_banner_name'    => $b->home_large_banner_name,
                'home_large_banner_image'   => $b->home_large_banner_image,
                'home_large_banner_position'=> $b->home_large_banner_position,

                /* foreign‑keys */
                'main_mcat_id'    => $b->main_mcat_id,
                'mcat_id'    => $b->mcat_id,
                'msubcat_id' => $b->msubcat_id,
                'mproduct_id'=> $b->mproduct_id,

                /* human‑readable names */
                'mcat_name'     => optional($b->category)->mcat_name,
                'msubcat_name'  => optional($b->subcategory)->msubcat_name,
                'mproduct_title'=> optional($b->product)->mproduct_title,
            ];
        });

        return response()->json([
        'status'       => true,
        'home_large_banner' => $banners
        ], 200);
    }


    public function largereorder(Request $request)
    {
        foreach ($request->all() as $item) {
            HomeLargeBanner::where('home_large_banner_id', $item['id'])
                ->update(['home_large_banner_position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }


    public function addLargeBanner(Request $request)
    {
        $request->validate([
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_id' => 'nullable|exists:mcategories,mcat_id',
            'msubcat_id' => 'nullable|exists:msubcategories,msubcat_id',
            'mproduct_id' => 'nullable|exists:mproducts,mproduct_id',
            'home_large_banner_name'  => 'required|string|max:50',
            'home_large_banner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $banner_imgpath = null;
        if ($request->hasFile('home_large_banner_image')) {
            $image  = $request->file('home_large_banner_image');
            $filename = 'home_large_banner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            $banner_imgpath      = 'goapp/images/home_large_banner/' . $filename;
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());
        }

        $home_large_banner  = new HomeLargeBanner();
        $home_large_banner->main_mcat_id    = $request->main_mcat_id;
        $home_large_banner->mcat_id    = $request->mcat_id;
        $home_large_banner->msubcat_id    = $request->msubcat_id;
        $home_large_banner->mproduct_id    = $request->mproduct_id;
        $home_large_banner->home_large_banner_name    = $request->home_large_banner_name;
        $home_large_banner->home_large_banner_image   = $banner_imgpath;
        $home_large_banner->home_large_banner_position = HomeLargeBanner::max('home_large_banner_position') + 1;
        $home_large_banner->save();

        return response()->json(['status' => true]);
    }

    public function editLargeBanner(Request $request)
    {
        $request->validate([
            'home_large_banner_id'    => 'required|exists:home_large_banners,home_large_banner_id',
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_id' => 'nullable|exists:mcategories,mcat_id',
            'msubcat_id' => 'nullable|exists:msubcategories,msubcat_id',
            'mproduct_id' => 'nullable|exists:mproducts,mproduct_id',
            'home_large_banner_name'  => 'required|string|max:255',
            'home_large_banner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $home_large_banner = HomeLargeBanner::find($request->home_large_banner_id);
        $home_large_banner->main_mcat_id  = $request->main_mcat_id;
        $home_large_banner->mcat_id  = $request->mcat_id;
        $home_large_banner->msubcat_id  = $request->msubcat_id;
        $home_large_banner->mproduct_id  = $request->mproduct_id;
        $home_large_banner->home_large_banner_name  = $request->home_large_banner_name;
        $banner_imgpath = $home_large_banner->home_large_banner_image;

        if ($request->hasFile('home_large_banner_image')) {
            if (!empty($banner_imgpath) && Storage::disk('s3')->exists($banner_imgpath)) {
                Storage::disk('s3')->delete($banner_imgpath);
            }
            $image = $request->file('home_large_banner_image');
            $filename = 'home_large_banner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            
            $banner_imgpath      = "goapp/images/home_large_banner/$filename";
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());

            $home_large_banner->home_large_banner_image = $banner_imgpath;
        }

        $home_large_banner->save();

        return response()->json(['status' => true]);
    }

    public function deleteLargeBanner(Request $request)
    {
        $request->validate([
            'home_large_banner_id'    => 'required|exists:home_large_banners,home_large_banner_id',
        ]);

        try {
            $home_large_banner = HomeLargeBanner::findOrFail($request->home_large_banner_id);

            if ($home_large_banner->home_large_banner_image && Storage::disk('s3')->exists($home_large_banner->home_large_banner_image)) {
                Storage::disk('s3')->delete($home_large_banner->home_large_banner_image);
            }

            $home_large_banner->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

// Home Small banner 

    public function smallBannerList()
    {
       return view('admin.banner.home_small_banner');
    }

    public function smallBannerVlist()
    {
        $banners = HomeSmallBanner::with([
            'category:mcat_id,mcat_name',
            'subcategory:msubcat_id,msubcat_name',
            'product:mproduct_id,mproduct_title'
        ])
        ->orderBy('home_small_banner_position')
        ->get()
        ->map(function ($b) {
            return [
                'home_small_banner_id'      => $b->home_small_banner_id,
                'home_small_banner_name'    => $b->home_small_banner_name,
                'home_small_banner_image'   => $b->home_small_banner_image,
                'home_small_banner_position'=> $b->home_small_banner_position,

                /* foreign‑keys */
                'main_mcat_id'    => $b->main_mcat_id,
                'mcat_id'    => $b->mcat_id,
                'msubcat_id' => $b->msubcat_id,
                'mproduct_id'=> $b->mproduct_id,

                /* human‑readable names */
                'mcat_name'     => optional($b->category)->mcat_name,
                'msubcat_name'  => optional($b->subcategory)->msubcat_name,
                'mproduct_title'=> optional($b->product)->mproduct_title,
            ];
        });

        return response()->json([
        'status'       => true,
        'home_small_banner' => $banners
        ], 200);
    }


    public function smallreorder(Request $request)
    {
        foreach ($request->all() as $item) {
            HomeSmallBanner::where('home_small_banner_id', $item['id'])
                ->update(['home_small_banner_position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }


    public function addSmallBanner(Request $request)
    {
        $request->validate([
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_id' => 'nullable|exists:mcategories,mcat_id',
            'msubcat_id' => 'nullable|exists:msubcategories,msubcat_id',
            'mproduct_id' => 'nullable|exists:mproducts,mproduct_id',
            'home_small_banner_name'  => 'required|string|max:50',
            'home_small_banner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $banner_imgpath = null;
        if ($request->hasFile('home_small_banner_image')) {
            $image  = $request->file('home_small_banner_image');
            $filename = 'home_small_banner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            $banner_imgpath      = 'goapp/images/home_small_banner/' . $filename;
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());
        }

        $home_small_banner  = new HomeSmallBanner();
        $home_small_banner->main_mcat_id    = $request->main_mcat_id;
        $home_small_banner->mcat_id    = $request->mcat_id;
        $home_small_banner->msubcat_id    = $request->msubcat_id;
        $home_small_banner->mproduct_id    = $request->mproduct_id;
        $home_small_banner->home_small_banner_name    = $request->home_small_banner_name;
        $home_small_banner->home_small_banner_image   = $banner_imgpath;
        $home_small_banner->home_small_banner_position = HomeSmallBanner::max('home_small_banner_position') + 1;
        $home_small_banner->save();

        return response()->json(['status' => true]);
    }

    public function editSmallBanner(Request $request)
    {
        $request->validate([
            'home_small_banner_id'    => 'required|exists:home_small_banners,home_small_banner_id',
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_id' => 'nullable|exists:mcategories,mcat_id',
            'msubcat_id' => 'nullable|exists:msubcategories,msubcat_id',
            'mproduct_id' => 'nullable|exists:mproducts,mproduct_id',
            'home_small_banner_name'  => 'required|string|max:255',
            'home_small_banner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $home_small_banner = HomeSmallBanner::find($request->home_small_banner_id);
        $home_small_banner->main_mcat_id  = $request->main_mcat_id;
        $home_small_banner->mcat_id  = $request->mcat_id;
        $home_small_banner->msubcat_id  = $request->msubcat_id;
        $home_small_banner->mproduct_id  = $request->mproduct_id;
        $home_small_banner->home_small_banner_name  = $request->home_small_banner_name;
        $banner_imgpath = $home_small_banner->home_small_banner_image;

        if ($request->hasFile('home_small_banner_image')) {
            if (!empty($banner_imgpath) && Storage::disk('s3')->exists($banner_imgpath)) {
                Storage::disk('s3')->delete($banner_imgpath);
            }
            $image = $request->file('home_small_banner_image');
            $filename = 'home_small_banner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            
            $banner_imgpath      = "goapp/images/home_small_banner/$filename";
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());

            $home_small_banner->home_small_banner_image = $banner_imgpath;
        }

        $home_small_banner->save();

        return response()->json(['status' => true]);
    }

    public function deleteSmallBanner(Request $request)
    {
        $request->validate([
            'home_small_banner_id'    => 'required|exists:home_small_banners,home_small_banner_id',
        ]);

        try {
            $home_small_banner = HomeSmallBanner::findOrFail($request->home_small_banner_id);

            if ($home_small_banner->home_small_banner_image && Storage::disk('s3')->exists($home_small_banner->home_small_banner_image)) {
                Storage::disk('s3')->delete($home_small_banner->home_small_banner_image);
            }

            $home_small_banner->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

// Home Explore Deal banner

    public function exploreDealBannerList()
    {
       return view('admin.banner.home_explore_deal_banner');
    }

    public function exploreDealBannerVlist()
    {
        $banners = HomeExploreDealBanner::with([
            'category:mcat_id,mcat_name',
            'subcategory:msubcat_id,msubcat_name',
            'product:mproduct_id,mproduct_title'
        ])
        ->orderBy('home_explore_deal_banner_position')
        ->get()
        ->map(function ($b) {
            return [
                'home_explore_deal_banner_id'      => $b->home_explore_deal_banner_id,
                'home_explore_deal_banner_name'    => $b->home_explore_deal_banner_name,
                'home_explore_deal_banner_image'   => $b->home_explore_deal_banner_image,
                'home_explore_deal_banner_position'=> $b->home_explore_deal_banner_position,

                /* foreign‑keys */
                'main_mcat_id'    => $b->main_mcat_id,
                'mcat_id'    => $b->mcat_id,
                'msubcat_id' => $b->msubcat_id,
                'mproduct_id'=> $b->mproduct_id,

                /* human‑readable names */
                'mcat_name'     => optional($b->category)->mcat_name,
                'msubcat_name'  => optional($b->subcategory)->msubcat_name,
                'mproduct_title'=> optional($b->product)->mproduct_title,
            ];
        });

        return response()->json([
        'status'       => true,
        'home_explore_deal_banner' => $banners
        ], 200);
    }


    public function exploreDealreorder(Request $request)
    {
        foreach ($request->all() as $item) {
            HomeExploreDealBanner::where('home_explore_deal_banner_id', $item['id'])
                ->update(['home_explore_deal_banner_position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }


    public function addExploreDealBanner(Request $request)
    {
        $request->validate([
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_id' => 'nullable|exists:mcategories,mcat_id',
            'msubcat_id' => 'nullable|exists:msubcategories,msubcat_id',
            'mproduct_id' => 'nullable|exists:mproducts,mproduct_id',
            'home_explore_deal_banner_name'  => 'required|string|max:50',
            'home_explore_deal_banner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $banner_imgpath = null;
        if ($request->hasFile('home_explore_deal_banner_image')) {
            $image  = $request->file('home_explore_deal_banner_image');
            $filename = 'home_explore_deal_banner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            $banner_imgpath      = 'goapp/images/home_explore_deal_banner/' . $filename;
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());
        }

        $home_explore_deal_banner  = new HomeExploreDealBanner();
        $home_explore_deal_banner->main_mcat_id    = $request->main_mcat_id;
        $home_explore_deal_banner->mcat_id    = $request->mcat_id;
        $home_explore_deal_banner->msubcat_id    = $request->msubcat_id;
        $home_explore_deal_banner->mproduct_id    = $request->mproduct_id;
        $home_explore_deal_banner->home_explore_deal_banner_name    = $request->home_explore_deal_banner_name;
        $home_explore_deal_banner->home_explore_deal_banner_image   = $banner_imgpath;
        $home_explore_deal_banner->home_explore_deal_banner_position = HomeExploreDealBanner::max('home_explore_deal_banner_position') + 1;
        $home_explore_deal_banner->save();

        return response()->json(['status' => true]);
    }

    public function editExploreDealBanner(Request $request)
    {
        $request->validate([
            'home_explore_deal_banner_id'    => 'required|exists:home_explore_deal_banners,home_explore_deal_banner_id',
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_id' => 'nullable|exists:mcategories,mcat_id',
            'msubcat_id' => 'nullable|exists:msubcategories,msubcat_id',
            'mproduct_id' => 'nullable|exists:mproducts,mproduct_id',
            'home_explore_deal_banner_name'  => 'required|string|max:255',
            'home_explore_deal_banner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $home_explore_deal_banner = HomeExploreDealBanner::find($request->home_explore_deal_banner_id);
        $home_explore_deal_banner->main_mcat_id  = $request->main_mcat_id;
        $home_explore_deal_banner->mcat_id  = $request->mcat_id;
        $home_explore_deal_banner->msubcat_id  = $request->msubcat_id;
        $home_explore_deal_banner->mproduct_id  = $request->mproduct_id;
        $home_explore_deal_banner->home_explore_deal_banner_name  = $request->home_explore_deal_banner_name;
        $banner_imgpath = $home_explore_deal_banner->home_explore_deal_banner_image;

        if ($request->hasFile('home_explore_deal_banner_image')) {
            if (!empty($banner_imgpath) && Storage::disk('s3')->exists($banner_imgpath)) {
                Storage::disk('s3')->delete($banner_imgpath);
            }
            $image = $request->file('home_explore_deal_banner_image');
            $filename = 'home_explore_deal_banner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            
            $banner_imgpath      = "goapp/images/home_explore_deal_banner/$filename";
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());

            $home_explore_deal_banner->home_explore_deal_banner_image = $banner_imgpath;
        }

        $home_explore_deal_banner->save();

        return response()->json(['status' => true]);
    }

    public function deleteExploreDealBanner(Request $request)
    {
        $request->validate([
            'home_explore_deal_banner_id'    => 'required|exists:home_explore_deal_banners,home_explore_deal_banner_id',
        ]);

        try {
            $home_explore_deal_banner = HomeExploreDealBanner::findOrFail($request->home_explore_deal_banner_id);

            if ($home_explore_deal_banner->home_explore_deal_banner_image && Storage::disk('s3')->exists($home_explore_deal_banner->home_explore_deal_banner_image)) {
                Storage::disk('s3')->delete($home_explore_deal_banner->home_explore_deal_banner_image);
            }

            $home_explore_deal_banner->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

// Home Fruit banner

    public function fruitBannerList()
    {
       return view('admin.banner.home_fruit_banner');
    }

    public function fruitBannerVlist()
    {
        $banners = HomeFruitBanner::with([
            'category:mcat_id,mcat_name',
            'subcategory:msubcat_id,msubcat_name',
            'product:mproduct_id,mproduct_title'
        ])
        ->orderBy('home_fruit_banner_position')
        ->get()
        ->map(function ($b) {
            return [
                'home_fruit_banner_id'      => $b->home_fruit_banner_id,
                'home_fruit_banner_name'    => $b->home_fruit_banner_name,
                'home_fruit_banner_image'   => $b->home_fruit_banner_image,
                'home_fruit_banner_position'=> $b->home_fruit_banner_position,

                /* foreign‑keys */
                'main_mcat_id'    => $b->main_mcat_id,
                'mcat_id'    => $b->mcat_id,
                'msubcat_id' => $b->msubcat_id,
                'mproduct_id'=> $b->mproduct_id,

                /* human‑readable names */
                'mcat_name'     => optional($b->category)->mcat_name,
                'msubcat_name'  => optional($b->subcategory)->msubcat_name,
                'mproduct_title'=> optional($b->product)->mproduct_title,
            ];
        });

        return response()->json([
        'status'       => true,
        'home_fruit_banner' => $banners
        ], 200);
    }


    public function fruitreorder(Request $request)
    {
        foreach ($request->all() as $item) {
            HomeFruitBanner::where('home_fruit_banner_id', $item['id'])
                ->update(['home_fruit_banner_position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }


    public function addFruitBanner(Request $request)
    {
        $request->validate([
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_id' => 'nullable|exists:mcategories,mcat_id',
            'msubcat_id' => 'nullable|exists:msubcategories,msubcat_id',
            'mproduct_id' => 'nullable|exists:mproducts,mproduct_id',
            'home_fruit_banner_name'  => 'required|string|max:50',
            'home_fruit_banner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $banner_imgpath = null;
        if ($request->hasFile('home_fruit_banner_image')) {
            $image  = $request->file('home_fruit_banner_image');
            $filename = 'home_fruit_banner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            $banner_imgpath      = 'goapp/images/home_fruit_banner/' . $filename;
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());
        }

        $home_fruit_banner  = new HomeFruitBanner();
        $home_fruit_banner->main_mcat_id    = $request->main_mcat_id;
        $home_fruit_banner->mcat_id    = $request->mcat_id;
        $home_fruit_banner->msubcat_id    = $request->msubcat_id;
        $home_fruit_banner->mproduct_id    = $request->mproduct_id;
        $home_fruit_banner->home_fruit_banner_name    = $request->home_fruit_banner_name;
        $home_fruit_banner->home_fruit_banner_image   = $banner_imgpath;
        $home_fruit_banner->home_fruit_banner_position = HomeFruitBanner::max('home_fruit_banner_position') + 1;
        $home_fruit_banner->save();

        return response()->json(['status' => true]);
    }

    public function editFruitBanner(Request $request)
    {
        $request->validate([
            'home_fruit_banner_id'    => 'required|exists:home_fruit_banners,home_fruit_banner_id',
            'main_mcat_id' => 'nullable|exists:main_categories,main_mcat_id',
            'mcat_id' => 'nullable|exists:mcategories,mcat_id',
            'msubcat_id' => 'nullable|exists:msubcategories,msubcat_id',
            'mproduct_id' => 'nullable|exists:mproducts,mproduct_id',
            'home_fruit_banner_name'  => 'required|string|max:255',
            'home_fruit_banner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $home_fruit_banner = HomeFruitBanner::find($request->home_fruit_banner_id);
        $home_fruit_banner->main_mcat_id  = $request->main_mcat_id;
        $home_fruit_banner->mcat_id  = $request->mcat_id;
        $home_fruit_banner->msubcat_id  = $request->msubcat_id;
        $home_fruit_banner->mproduct_id  = $request->mproduct_id;
        $home_fruit_banner->home_fruit_banner_name  = $request->home_fruit_banner_name;
        $banner_imgpath = $home_fruit_banner->home_fruit_banner_image;

        if ($request->hasFile('home_fruit_banner_image')) {
            if (!empty($banner_imgpath) && Storage::disk('s3')->exists($banner_imgpath)) {
                Storage::disk('s3')->delete($banner_imgpath);
            }
            $image = $request->file('home_fruit_banner_image');
            $filename = 'home_fruit_banner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            
            $banner_imgpath      = "goapp/images/home_fruit_banner/$filename";
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());

            $home_fruit_banner->home_fruit_banner_image = $banner_imgpath;
        }

        $home_fruit_banner->save();

        return response()->json(['status' => true]);
    }

    public function deleteFruitBanner(Request $request)
    {
        $request->validate([
            'home_fruit_banner_id'    => 'required|exists:home_fruit_banners,home_fruit_banner_id',
        ]);

        try {
            $home_fruit_banner = HomeFruitBanner::findOrFail($request->home_fruit_banner_id);

            if ($home_fruit_banner->home_fruit_banner_image && Storage::disk('s3')->exists($home_fruit_banner->home_fruit_banner_image)) {
                Storage::disk('s3')->delete($home_fruit_banner->home_fruit_banner_image);
            }

            $home_fruit_banner->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
