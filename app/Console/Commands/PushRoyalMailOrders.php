<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Order;
use App\Services\ClickDropService;

class PushRoyalMailOrders extends Command
{
    protected $signature = 'royalmail:push
    {orderId? : Push a single order by ID}
    {--all : Push all eligible orders}
    {--limit=0 : Limit the number of orders when using --all}';
    protected $description = 'Push an order to Royal Mail Click & Drop immediately (no queue)';

    public function handle(ClickDropService $svc)
    {
        $orderId = $this->argument('orderId');
        $all = $this->option('all');
        $limit = (int) $this->option('limit');

        if ($all) {
            $query = Order::with(['user', 'userCompanyAddress', 'deliveryMethod'])
                ->whereNull('royalmail_order_identifier'); // only those not yet pushed

            if ($limit > 0) {
                $query->limit($limit);
            }

            $orders = $query->get();

            if ($orders->isEmpty()) {
                $this->warn("No orders found to push.");
                return 0;
            }

            foreach ($orders as $o) {
                $res = $svc->pushOrder($svc->mapFromModel($o));

                $identifier = data_get($res, 'createdOrders.0.orderIdentifier')
                    ?? data_get($res, 'orders.0.orderIdentifier')
                    ?? data_get($res, 'items.0.orderIdentifier')
                    ?? null;

                $success = (int) data_get($res, 'successCount', 0) > 0;

                $o->update([
                    'royalmail_order_identifier' => $identifier,
                    'pushed_to_cnd_at' => now(),
                    'cnd_status' => $success ? 'created' : 'failed',
                    'cnd_last_error' => $success ? null : json_encode($res),
                ]);

                $this->info("Order {$o->order_id} pushed. Status: " . ($success ? 'created' : 'failed'));
            }
            return 0;
        }

        if ($orderId) {
            $o = Order::with(['user', 'userCompanyAddress', 'deliveryMethod'])->find($orderId);
            if (!$o) {
                $this->error("Order $orderId not found");
                return 1;
            }

            $res = $svc->pushOrder($svc->mapFromModel($o));

            $identifier = data_get($res, 'createdOrders.0.orderIdentifier')
                ?? data_get($res, 'orders.0.orderIdentifier')
                ?? data_get($res, 'items.0.orderIdentifier')
                ?? null;

            $success = (int) data_get($res, 'successCount', 0) > 0;

            $o->update([
                'royalmail_order_identifier' => $identifier,
                'pushed_to_cnd_at' => now(),
                'cnd_status' => $success ? 'created' : 'failed',
                'cnd_last_error' => $success ? null : json_encode($res),
            ]);

            $this->info('Response: ' . json_encode($res));
            return 0;
        }

        $this->error("You must provide either {orderId} or use --all");
        return 1;
    }

}
