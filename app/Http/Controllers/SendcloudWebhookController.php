<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class SendcloudWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('Sendcloud Webhook Received', $request->all());

        $payload = $request->all();

        if (!isset($payload['parcel'])) {
            return response()->json(['error' => 'Invalid payload'], 400);
        }

        $parcel = $payload['parcel'];

        $orderNumber = $parcel['order_number'] ?? null;
        $orderId = str_replace('#TR00', '', $orderNumber); 

        if (!$orderId) {
            return response()->json(['error' => 'Order ID not found'], 400);
        }

        $tracking = $parcel['tracking_number'] ?? null;
        $labelUrl = $parcel['label']['normal_printer'][0] ?? null;

        Order::where('order_id', $orderId)->update([
            'tracking_number' => $tracking,
            'label_url' => $labelUrl,
            'shipment_status' => $payload['status']['message'] ?? 'updated',
        ]);

        return response()->json(['success' => true]);
    }
}
