<?php

namespace App\Services;

use App\Models\IntegrationSetting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class SendcloudService
{
    protected string $base;
    protected string $public;
    protected string $secret;

    // Defaults
    private int $senderAddressId = 701837;   // your sender address id
    private int $defaultShipmentId = 8;      // Royal Mail Tracked 48
    private float $defaultItemWeightKg = 0.5;
    private int $len = 10, $wid = 10, $hgt = 5;
    private bool $autoLabel = true;

    public function __construct()
    {
        $settings = IntegrationSetting::where('provider', 'sendcloud')->where('is_active', true)->first();

        $this->base   = rtrim((string) config('services.sendcloud.base'), '/');
        $this->public = $settings->public_key ?? null;
        $this->secret = $settings->secret_key ?? null;
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => 'Basic '.base64_encode($this->public.':'.$this->secret),
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ];
    }

    public function pushToIncomingOrders(Order $order): array
    {
        $items = [];
        $totalWeightKg = 0.0;
        $totalValue = 0.0;

        foreach ($order->items as $row) {
            $variant = $row->variant;
            $product = $variant?->product;

            $rawW   = (float) ($variant->weight ?? 0.0);
            $unit   = strtolower((string) ($variant->weightunit ?? 'kg'));
            $wKg    = $rawW > 0 ? ($unit === 'g' ? max(0.01, $rawW / 1000) : max(0.01, $rawW)) : $this->defaultItemWeightKg;
            $qty    = (int) ($row->quantity ?? 1);
            $price  = (float) ($row->unit_price ?? $row->price ?? 0.0);

            $items[] = [
                'description'    => substr((string) ($product->mproduct_title ?? $product->product_title ?? 'Item'), 0, 50),
                'quantity'       => $qty,
                'weight'         => $wKg,
                'value'          => $price,
                'origin_country' => strtoupper((string) ($order->userCompanyAddress->company_country ?? 'GB')),
                'sku'            => (string) ($variant->sku ?? ('SKU-'.$variant->mvariant_id)),
                'product_type'   => 'goods',
                // 'hs_code'      => (string) ($variant->hs_code ?? '610910'),
            ];

            $totalWeightKg += $wKg * $qty;
            $totalValue    += $price * $qty;
        }

        if ($totalWeightKg <= 0) $totalWeightKg = max($this->defaultItemWeightKg, 0.1);

        $addr1 = trim((string) ($order->userCompanyAddress->company_address1 ?? ''));
        $addr2 = trim((string) ($order->userCompanyAddress->company_address2 ?? ''));
        $address     = $addr1 ?: 'Unknown street';
        $houseNumber = '1';

        $shipmentId = $this->mapShipmentByName((string) ($order->deliveryMethod->delivery_method_name ?? ''));

        $payload = [
            'parcel' => [
                'sender_address'           => $this->senderAddressId,
                'name'                     => (string) ($order->user->name ?? 'Customer'),
                'company_name'             => (string) ($order->userCompanyAddress->user_company_name ?? ''),
                'address'                  => $address,
                'address_2'                => $addr2 ? $addr1 : null,
                'house_number'             => $houseNumber,
                'city'                     => (string) ($order->userCompanyAddress->company_city ?? ''),
                'postal_code'              => (string) ($order->userCompanyAddress->company_postcode ?? ''),
                'country'                  => strtoupper((string) ($order->userCompanyAddress->company_country ?? 'GB')),
                'email'                    => (string) ($order->user->email ?? ''),
                'telephone'                => (string) ($order->user->mobile ?? ''),
                'weight'                   => round($totalWeightKg, 3),
                'length'                   => $this->len,
                'width'                    => $this->wid,
                'height'                   => $this->hgt,
                'order_number'             => (string) $order->order_id,
                'external_reference'       => 'Order-'.$order->order_id,
                'reference'                => 'Order-'.$order->order_id,
                'integration'              => 524494,
                'request_label'            => $this->autoLabel,
                'shipment'                 => ['id' => $shipmentId],
                'total_order_value'        => round($totalValue, 2),
                'total_order_value_currency' => 'GBP',
                'customs_declaration' => [
                    'invoice_number' => (string) $order->order_id,
                    'items'          => $items,
                ],
                "parcel_items" => $items,
            ],
        ];

        $res = Http::withHeaders($this->authHeaders())->post($this->base.'/parcels', $payload);
        Log::info('Sendcloud Push', ['order' => $order->order_id, 'status' => $res->status(), 'body' => $res->json()]);

        if ($res->successful() && isset($res['parcel'])) {
            $p   = $res['parcel'];
            $pid = $p['id'] ?? null;
            $trk = $p['tracking_number'] ?? null;

            $labelUrl = $p['label']['label_printer'] ?? null;
            if (!$labelUrl && !empty($p['label']['normal_printer'])) $labelUrl = $p['label']['normal_printer'][0] ?? null;
            if (!$labelUrl && !empty($p['documents'])) {
                foreach ($p['documents'] as $d) if (($d['type'] ?? '') === 'label') { $labelUrl = $d['link'] ?? null; break; }
            }

            $order->update([
                'sendcloud_parcel_id' => $pid,
                'tracking_number'     => $trk,
                'label_url'           => $labelUrl,
                'cnd_status'          => 'created',
                'pushed_to_cnd_at'    => now(),
                'cnd_last_error'      => null,
            ]);

            return $res->json();
        }

        $order->update([
            'cnd_status'       => 'failed',
            'cnd_last_error'   => $res->body(),
            'pushed_to_cnd_at' => now(),
        ]);

        return ['ok' => false, 'status' => $res->status(), 'error' => $res->json() ?? $res->body()];
    }

    private function mapShipmentByName(string $deliveryMethodName): int
    {
        // dynamic mapping (future): fill this array; default remains 8
        $map = [
            // 'ROYAL MAIL TRACKED 24 - SMALL PARCEL' => 7,
            // 'ROYAL MAIL TRACKED 48 - SMALL PARCEL' => 8,
            // 'DHL EXPRESS' => 65,
        ];
        $key = strtoupper(trim($deliveryMethodName));
        return $map[$key] ?? $this->defaultShipmentId;
    }
}
