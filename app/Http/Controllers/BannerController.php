<?php

namespace App\Http\Controllers;

use App\Models\Browsebanner;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class BannerController extends Controller
{
    public function browseBannerList()
    {
       return view('admin.banner.browsebanner');
    }

    public function browseBannerVlist()
    {
        $browsebanner = Browsebanner::orderBy('browsebanner_position')->get();

        return response()->json([
            'status' => true,
            'browsebanner' => $browsebanner,
        ], 200);
    }


    public function reorder(Request $request)
    {
        foreach ($request->all() as $item) {
            Browsebanner::where('browsebanner_id', $item['id'])
                ->update(['browsebanner_position' => $item['position']]);
        }

        return response()->json(['success' => true]);
    }


    public function addBrowseBanner(Request $request)
    {
        $request->validate([
            'mcat_id' => 'required|exists:mcategories,mcat_id',
            'browsebanner_name'  => 'required|string|max:50',
            'browsebanner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $banner_imgpath = null;
        if ($request->hasFile('browsebanner_image')) {
            $image  = $request->file('browsebanner_image');
            $filename = 'browsebanner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            $banner_imgpath      = 'goapp/images/browsebanner/' . $filename;
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());
        }

        $browsebanner  = new Browsebanner();
        $browsebanner->mcat_id    = $request->mcat_id;
        $browsebanner->browsebanner_name    = $request->browsebanner_name;
        $browsebanner->browsebanner_image   = $banner_imgpath;
        $browsebanner->browsebanner_position = Browsebanner::max('browsebanner_position') + 1;
        $browsebanner->save();

        return response()->json(['status' => true]);
    }

    public function editBrowseBanner(Request $request)
    {
        $request->validate([
            'browsebanner_id'    => 'required|exists:browsebanners,browsebanner_id',
            'mcat_id'            => 'required|exists:mcategories,mcat_id',
            'browsebanner_name'  => 'required|string|max:255',
            'browsebanner_image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $browsebanner = Browsebanner::find($request->browsebanner_id);
        $browsebanner->mcat_id  = $request->mcat_id;
        $browsebanner->browsebanner_name  = $request->browsebanner_name;
        $banner_imgpath = $browsebanner->browsebanner_image;

        if ($request->hasFile('browsebanner_image')) {
            if (!empty($banner_imgpath) && Storage::disk('s3')->exists($banner_imgpath)) {
                Storage::disk('s3')->delete($banner_imgpath);
            }
            $image = $request->file('browsebanner_image');
            $filename = 'browsebanner_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            
            $banner_imgpath      = "goapp/images/browsebanner/$filename";
            Storage::disk('s3')->put($banner_imgpath, (string) $img->encode());

            $browsebanner->browsebanner_image = $banner_imgpath;
        }

        $browsebanner->save();

        return response()->json(['status' => true]);
    }
}
