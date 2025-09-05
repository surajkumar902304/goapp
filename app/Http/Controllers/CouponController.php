<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\ServiceSolution;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;

class CouponController extends Controller
{
  public function index()
{
    $coupons = Coupon::with('mainCategory')
        ->orderBy('coupon_id', 'desc')
        ->get()
        ->map(function ($c) {
            return [
                'coupon_id'       => $c->coupon_id,
                'code'            => $c->code,
                'main_mcat_id'    => $c->main_mcat_id,
                'main_mcat_name'  => optional($c->mainCategory)->main_mcat_name ??null, // displayable name
                'discount_type'   => $c->discount_type,
                'discount_value'  => $c->discount_value,
                'min_cart_value'  => $c->min_cart_value,
                'usage_limit'      => $c->usage_limit,     
                'per_user_limit'   => $c->per_user_limit ,
                'expires_at'      => $c->expires_at ? $c->expires_at->format('Y-m-d') :null,
                'is_active'       => $c->is_active,
            ];
        });

    return response()->json([
        'status' => true,
        'coupons' => $coupons,
    ]);
}

    public function addCoupon(Request $request)
    {
        $request->validate([
            'code'              => ['required', 'string', 'max:50', 'unique:coupons,code'],
            'discount_type'     => ['required', Rule::in(['fixed','percent'])],
            'discount_value'    => ['required', 'numeric', 'min:0'],
            'expires_at'        => ['nullable', 'date'],
            'usage_limit'       => ['nullable', 'integer', 'min:1'],
            'per_user_limit'    => ['nullable', 'integer', 'min:1'],
            'min_cart_value'    => ['nullable', 'numeric', 'min:0'],
            'main_mcat_id' => ['nullable', 'exists:main_categories,main_mcat_id'],


        ]);

        $coupon                 = new Coupon();
        $coupon->code           = $request->code;
        $coupon->discount_type  = $request->discount_type;
        $coupon->discount_value = $request->discount_value;
        $coupon->expires_at     = $request->expires_at ?? null;
        $coupon->usage_limit    = $request->usage_limit ?? null;
        $coupon->per_user_limit = $request->per_user_limit ?? null;
        $coupon->min_cart_value = $request->min_cart_value ?? 0.00;
        $coupon->main_mcat_id   = $request->main_mcat_id;
        $coupon->save();

        return response()->json(['status' => true]);
    }

    public function couponToggleStatus(Request $request, $id)
    {
        $coupon = Coupon::findOrFail($id);
        $coupon->is_active = $request->is_active;
        $coupon->save();

        return response()->json(['status' => true, 'message' => 'Status updated.']);
    }

    public function editCoupon(Request $request)
    {
        $request->validate([
            'coupon_id'    => 'required|exists:coupons,coupon_id',
            'code'             => ['required', 'string', 'max:50', 
                Rule::unique('coupons','code')->ignore($request->coupon_id, 'coupon_id')
            ],
            'discount_type'     => ['required', Rule::in(['fixed','percent'])],
            'discount_value'    => ['required', 'numeric', 'min:0'],
            'expires_at'        => ['nullable', 'date'],
            'usage_limit'       => ['nullable', 'integer', 'min:1'],
            'per_user_limit'    => ['nullable', 'integer', 'min:1'],
            'min_cart_value'    => ['nullable', 'numeric', 'min:0'],
            'main_mcat_id' => ['nullable', 'exists:main_categories,main_mcat_id'],

        ]);

        $coupon = Coupon::find($request->coupon_id);
        $coupon->code           = $request->code;
        $coupon->discount_type  = $request->discount_type;
        $coupon->discount_value = $request->discount_value;
        $coupon->expires_at     = $request->expires_at ?? null;
        $coupon->usage_limit    = $request->usage_limit ?? null;
        $coupon->per_user_limit = $request->per_user_limit ?? null;
        $coupon->min_cart_value = $request->min_cart_value ?? 0.00;
        $coupon->main_mcat_id = $request->main_mcat_id;

        $coupon->save();

        return response()->json(['status' => true]);
    }

    public function deleteCoupon(Request $request)
    {
        $request->validate([
            'coupon_id' => 'required|exists:coupons,coupon_id',
        ]);

        try {
            $coupon = Coupon::findOrFail($request->coupon_id);

            $coupon->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkDeleteCoupon(Request $request)
    {
        $data = $request->validate([
            'coupon_ids'   => 'required|array',
            'coupon_ids.*' => 'integer|exists:coupons,coupon_id',
        ]);

        DB::transaction(function () use ($data) {
            Coupon::whereIn('coupon_id', $data['coupon_ids'])->delete();

        });

        return response()->json(['status' => true]);
    }


    // Services & Display Solutions routes
    public function serviceVlist()
    {
        $services = ServiceSolution::get();
        return response()->json([
            'status' => true,
            'services' => $services,
        ],200);
    }

    public function addService(Request $request)
    {
        $request->validate([
            'service_solution_title'     => ['required', 'string', 'max:255'],
            'service_solution_image'     => ['required', 'image', 'mimes:jpg,jpeg,png|max:2048'],
            'service_solution_sub_title' => ['required', 'string', 'max:255'],
            'service_solution_desc'      => ['required', 'string', 'max:255'],
        ]);

        $service_imgpath = null;
        if ($request->hasFile('service_solution_image')) {
            $image  = $request->file('service_solution_image');
            $filename = 'service_solution_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            $service_imgpath      = 'goapp/images/service_solution/' . $filename;
            Storage::disk('s3')->put($service_imgpath, (string) $img->encode());
        }

        $service = new ServiceSolution();
        $service->service_solution_title      = $request->service_solution_title;
        $service->service_solution_sub_title  = $request->service_solution_sub_title;
        $service->service_solution_image      = $service_imgpath;
        $service->service_solution_desc       = $request->service_solution_desc;
        $service->save();

        return response()->json(['status' => true]);
    }

    public function editService(Request $request)
    {
        $request->validate([
            'service_solution_id'        => 'required|exists:service_solutions,service_solution_id',
            'service_solution_title'     => ['required', 'string', 'max:255'],
            'service_solution_image'     => ['nullable', 'image', 'mimes:jpg,jpeg,png|max:2048'],
            'service_solution_sub_title' => ['required', 'string', 'max:255'],
            'service_solution_desc'      => ['required', 'string', 'max:255'],
        ]);

        $service = ServiceSolution::find($request->service_solution_id);
        $service->service_solution_title      = $request->service_solution_title;
        $service->service_solution_sub_title  = $request->service_solution_sub_title;
        $service_imgpath                      = $service->service_solution_image;
        $service->service_solution_desc       = $request->service_solution_desc;

        if ($request->hasFile('service_solution_image')) {
            if (!empty($service_imgpath) && Storage::disk('s3')->exists($service_imgpath)) {
                Storage::disk('s3')->delete($service_imgpath);
            }
            $image = $request->file('service_solution_image');
            $filename = 'service_solution_' . uniqid() . '.png';
            $img = Image::make($image->getRealPath());
            
            $service_imgpath      = "goapp/images/service_solution/$filename";
            Storage::disk('s3')->put($service_imgpath, (string) $img->encode());

            $service->service_solution_image = $service_imgpath;
        }

        $service->save();

        return response()->json(['status' => true]);
    }

    public function deleteService(Request $request)
    {
        $request->validate([
            'service_solution_id' => 'required|exists:service_solutions,service_solution_id',
        ]);

        try {
            $service = ServiceSolution::findOrFail($request->service_solution_id);

            if ($service->service_solution_image && Storage::disk('s3')->exists($service->service_solution_image)) {
                Storage::disk('s3')->delete($service->service_solution_image);
            }

            $service->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkDeleteService(Request $request)
    {
        $data = $request->validate([
            'service_solution_ids'   => 'required|array',
            'service_solution_ids.*' => 'integer|exists:service_solutions,service_solution_id',
        ]);

        DB::transaction(function() use ($data) {
            $services = ServiceSolution::whereIn('service_solution_id', $data['service_solution_ids'])->get();
            foreach ($services as $svc) {
                if ($svc->service_solution_image && Storage::disk('s3')->exists($svc->service_solution_image)) {
                    Storage::disk('s3')->delete($svc->service_solution_image);
                }
                $svc->delete();
            }
        });

        return response()->json(['status' => true]);
    }

}
