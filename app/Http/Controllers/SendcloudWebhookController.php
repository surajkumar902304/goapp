<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Log;

class SendcloudWebhookController extends Controller
{
    public function handle(Request $request)
    {
        Log::info('✅ Sendcloud Webhook Received:', $request->all());
        // file_put_contents('sendcloud_log.txt', json_encode($request->all(), JSON_PRETTY_PRINT), FILE_APPEND);

        $payload = $request->all();

        if (!isset($payload['parcel']['id'])) {
            return response()->json(['message' => 'No parcel data'], 200); 
        }

        $parcel = $payload['parcel'];
        $tracking = $parcel['tracking_number'] ?? null;
        $labelUrl = $parcel['label']['normal_printer'][0] ?? null;
        $shipmentStatus = $parcel['status']['message'] ?? 'updated';
        $sendcloudParcelId = $parcel['id'];

        // Find order using sendcloud_parcel_id
        $order = Order::where('sendcloud_parcel_id', $sendcloudParcelId)->first();

        if ($order) {
            $order->update([
                'tracking_number' => $tracking,
                'label_url' => $labelUrl,
                'shipment_status' => $shipmentStatus,
                'updated_at' => now(),
            ]);

            Log::info('✅ Order updated from webhook', [
                'order_id' => $order->order_id,
                'tracking_number' => $tracking,
                'status' => $shipmentStatus
            ]);
        } else {
            Log::warning('⚠ Order not found for parcel id ' . $sendcloudParcelId);
        }

        return response()->json(['message' => 'Webhook processed'], 200);
    }
}
