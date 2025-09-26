<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Order;
use App\Services\ClickDropService;

class RoyalMailSyncController extends Controller
{
    public function sync(ClickDropService $svc)
    {
        $pushedNow = $this->pushEligibleBatch($svc, 50);

        return response()->json([
            'ok' => true,
            'pushed_now' => $pushedNow
        ]);
    }

    public function pushEligibleBatch(ClickDropService $svc, int $limit = 200): int
    {
        $orders = Order::with([
            'user',
            'userCompanyAddress',
            'deliveryMethod',
            'items.variant.product',
        ])->where('fulfillment_status', 'unfulfilled')
            ->where(function ($q) {
                $q->whereNull('royalmail_order_identifier')
                    ->orWhere('cnd_status', '!=', 'created');
            })
            ->orderBy('order_id')
            ->limit($limit)
            ->get();

        $count = 0;

        foreach ($orders as $o) {
            $res = $svc->pushOrder($svc->mapFromModel($o));

            $identifier = data_get($res, 'createdOrders.0.orderIdentifier')
                ?? data_get($res, 'orders.0.orderIdentifier')
                ?? data_get($res, 'items.0.orderIdentifier')
                ?? data_get($res, 'orderIdentifier');

            $success = (int) data_get($res, 'successCount', 0) > 0;

            $o->update([
                'royalmail_order_identifier' => $identifier,
                'pushed_to_cnd_at' => now(),
                'cnd_status' => $success ? 'created' : 'failed',
                'cnd_last_error' => $success ? null : json_encode($res),
            ]);

            $count++;
        }

        return $count;
    }

}