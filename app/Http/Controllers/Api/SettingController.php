<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getMinOrder()
    {
        $value = Setting::where('key', 'min_order_free_delivery')->value('value') ?? '0';
        return response()->json([
            'status' => true,
            'message' => 'Minimum Order Free Delivery',
            'min_order_free_delivery' => $value,
        ]);
    }
}
