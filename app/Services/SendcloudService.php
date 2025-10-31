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
    private float $defaultItemWeightKg = 0.5;

    public function __construct()
    {
        $settings = IntegrationSetting::where('provider', 'sendcloud')->where('is_active', true)->first();

        $this->base = rtrim((string) config('services.sendcloud.base'), '/');
        $this->public = $settings->public_key ?? null;
        $this->secret = $settings->secret_key ?? null;
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => 'Basic ' . base64_encode($this->public . ':' . $this->secret),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function pushToIncomingOrders(Order $order): array
    {
        $items = [];
        $totalWeightKg = 0.0;

        foreach ($order->items as $row) {
            $variant = $row->variant;
            $product = $variant?->product;
            $details = $variant?->mvariantDetail;

            $optionValue = $details->option_value ?? [];
            if (is_string($optionValue)) {
                $optionValue = json_decode($optionValue, true);
            }
            if (!is_array($optionValue)) {
                $optionValue = [];
            }

            // ✅ Collect properties
            $properties = [];
            foreach ($optionValue as $key => $val) {
                $properties[strtolower($key)] = (string) $val;
            }

            // ✅ Human-readable variant text for packing slip
            $propertiesText = '';
            if (!empty($properties)) {
                $propertiesText = collect($properties)
                    ->map(fn($val, $key) => ucfirst($key) . ': ' . $val)
                    ->implode(', ');
            }

            $rawW = (float) ($variant->weight ?? 0.0);
            $unit = strtolower((string) ($variant->weightunit ?? 'kg'));
            $wKg = $rawW > 0 ? ($unit === 'g' ? max(0.01, $rawW / 1000) : max(0.01, $rawW)) : $this->defaultItemWeightKg;
            $qty = (int) ($row->quantity ?? 1);
            $price = (float) ($row->unit_price ?? $row->price ?? 0.0);

            $items[] = [
                'description' => substr((string) ($product->mproduct_title ?? $product->product_title ?? 'Item'), 0, 50),
                'quantity' => $qty,
                'weight' => $wKg,
                'value' => $price,
                'origin_country' => strtoupper((string) ($order->userCompanyAddress->company_country ?? 'GB')),
                'sku' => (string) ($variant->sku ?? ('SKU-' . $variant->mvariant_id)),
                'properties' => (object) $properties,
            ];

            $totalWeightKg += $wKg * $qty;
        }

        if ($totalWeightKg <= 0)
            $totalWeightKg = max($this->defaultItemWeightKg, 0.1);

        $addr1 = trim((string) ($order->userCompanyAddress->company_address1 ?? ''));
        $addr2 = trim((string) ($order->userCompanyAddress->company_address2 ?? ''));

        $payload = [
            'parcel' => [
                // 'sender_address'           => $this->senderAddressId,
                'name' => (string) ($order->user->name ?? 'Customer'),
                'company_name' => (string) ($order->userCompanyAddress->user_company_name ?? ''),
                'address' => $addr1,
                'address_2' => $addr2 ?? null,
                'city' => (string) ($order->userCompanyAddress->company_city ?? ''),
                'postal_code' => (string) ($order->userCompanyAddress->company_postcode ?? ''),
                'country' => strtoupper((string) ($order->userCompanyAddress->company_country ?? 'GB')),
                'email' => (string) ($order->user->email ?? ''),
                'telephone' => (string) ($order->user->mobile ?? ''),
                'weight' => round($totalWeightKg, 3),
                'order_number' => (string) '#TR00' . $order->order_id,
                'request_label' => false,
                'total_order_value' => round($order->total_paid, 2),
                'total_order_value_currency' => 'GBP',

                // 'order_shop_status'     => $order->fulfillment_status ?? 'unfulfilled',
                // 'order_payment_status'  => $order->status ?? 'unpaid',
                // 'shipping_method_checkout_name' => (string) ($order->deliveryMethod->delivery_method_name ?? ''),

                "parcel_items" => $items,
            ],
        ];

        $res = Http::withHeaders($this->authHeaders())->post($this->base . '/parcels', $payload);
        Log::info('Sendcloud Push', ['order' => $order->order_id, 'status' => $res->status(), 'body' => $res->json()]);

        if ($res->successful() && isset($res['parcel'])) {
            $p = $res['parcel'];
            $pid = $p['id'] ?? null;

            $order->update([
                'sendcloud_parcel_id' => $pid,
                'cnd_status' => 'created',
                'pushed_to_cnd_at' => now(),
                'cnd_last_error' => null,
            ]);

            return $res->json();
        }

        $order->update([
            'cnd_status' => 'failed',
            'cnd_last_error' => $res->body(),
            'pushed_to_cnd_at' => now(),
        ]);

        return ['ok' => false, 'status' => $res->status(), 'error' => $res->json() ?? $res->body()];
    }

}