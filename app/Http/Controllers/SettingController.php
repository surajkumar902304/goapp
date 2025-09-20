<?php

namespace App\Http\Controllers;

use App\Models\DeliveryMethod;
use App\Models\ProductVat;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // Min Ddelivery Order
    public function getMinOrder()
    {
        $setting = Setting::where('key', 'min_order_free_delivery')->first();
        return response()->json(['value' => $setting->value ?? '']);
    }

    public function saveMinOrder(Request $request)
    {
        Setting::updateOrCreate(
            ['key' => 'min_order_free_delivery'],
            ['value' => $request->value]
        );

        return response()->json(['message' => 'Updated']);
    }

    public function getListSettings()
    {
        $settings= Setting::where('key', 'min_order_free_delivery')->get();
        return response()->json([
            'status' => true,
            'deliverymethods' => $settings,
        ],200);

    }

    public function toggle(Setting $setting, Request $request)
    {
        $request->validate([
            'is_active' => 'required|boolean'
        ]);

        $setting->is_active = $request->input('is_active');
        $setting->save();

        return response()->json([
            'status'  => true,
            'setting' => $setting,
        ], 200);
    }

    // Delivery Methods
    public function deliveryMethodVlist()
    {
        $deliverymethods = DeliveryMethod::orderBy('delivery_method_id', 'desc')->get();
        return response()->json([
            'status' => true,
            'deliverymethods' => $deliverymethods,
        ],200);
    }

    public function addDeliveryMethod(Request $request)
    {
        $request->validate([
            'delivery_method_name'  => ['required', 'string', 'max:255'],
            'delivery_method_amount' => ['required', 'numeric'],
        ]);

        $bank_detail = new DeliveryMethod();
        $bank_detail->delivery_method_name = $request->delivery_method_name;
        $bank_detail->delivery_method_amount = $request->delivery_method_amount;
        $bank_detail->save();

        return response()->json(['status' => true]);
    }

    public function deliveryMethodToggleStatus(Request $request, $id)
    {
        $bank_detail = DeliveryMethod::findOrFail($id);
        $bank_detail->is_active = $request->is_active;
        $bank_detail->save();

        return response()->json(['status' => true, 'message' => 'Status updated.']);
    }

    public function editDeliveryMethod(Request $request)
    {
        $request->validate([
            'delivery_method_id'   => 'required|exists:delivery_methods,delivery_method_id',
            'delivery_method_name' => ['required', 'string', 'max:255'],
            'delivery_method_amount' => ['required', 'numeric'],
        ]);

        $bank_detail = DeliveryMethod::find($request->delivery_method_id);
        $bank_detail->delivery_method_name = $request->delivery_method_name;
        $bank_detail->delivery_method_amount = $request->delivery_method_amount;
        $bank_detail->save();

        return response()->json(['status' => true]);
    }

    public function deleteDeliveryMethod(Request $request)
    {
        $request->validate([
            'delivery_method_id' => 'required|exists:delivery_methods,delivery_method_id',
        ]);

        try {
            $bank_detail = DeliveryMethod::findOrFail($request->delivery_method_id);

            $bank_detail->delete();

            return response()->json(['status' => true]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    // Product Vat
    public function productVatVlist()
    {
        $vat = ProductVat::get();
        return response()->json([
            'status' => true,
            'vat' => $vat,
        ],200);
    }

    public function editProductVat(Request $request)
    {
        $request->validate([
            'product_vat_id' => 'required|exists:product_vats,product_vat_id',
            'product_vat'    => 'required|integer',
        ]);

        $vat = ProductVat::find($request->product_vat_id);
        $vat->product_vat = $request->product_vat;
        $vat->save();

        return response()->json(['status' => true]);
    }
}
