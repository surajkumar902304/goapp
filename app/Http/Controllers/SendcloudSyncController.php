<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\SendcloudService;

class SendcloudSyncController extends Controller
{
    public function syncSendcloud(SendcloudService $svc)
    {
        $orders = Order::whereNull('sendcloud_parcel_id')
            ->where('fulfillment_status', 'unfulfilled')
            ->get();

        $success = 0;
        $failed  = 0;
        $errors  = [];

        foreach ($orders as $order) {
            $result = $svc->pushToIncomingOrders($order);

            if (isset($result['parcel']['id'])) {
                $success++;
            } else {
                $failed++;
                $errors[] = [
                    'order_id' => $order->order_id,
                    'error'    => $result['error'] ?? 'Unknown Sendcloud error'
                ];
            }
        }

        return response()->json([
            'ok' => true,
            'total_orders' => $orders->count(),
            'successful' => $success,
            'failed' => $failed,
            'errors' => $errors,
            'pushed_now' => $success
        ]);
    }
}
