<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function getMinOrder()
    {
        $orderFree = Setting::where('key', 'min_order_free_delivery')->value('value') ?? '0';
        $orderPlace = Setting::where('key', 'min_order_place')->value('value') ?? '0';
        return response()->json([
            'status' => true,
            'min_order_free_delivery' => $orderFree,
            'min_order_place' => $orderPlace,
        ]);
    }
}
