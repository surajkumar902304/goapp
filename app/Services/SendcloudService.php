<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Order;

class SendcloudService
{
    protected string $base;
    protected string $public;
    protected string $secret;

    public function __construct()
    {
        $this->base   = config('services.sendcloud.base');
        $this->public = config('services.sendcloud.public');
        $this->secret = config('services.sendcloud.secret');
    }

    private function authHeaders()
    {
        return [
            'Authorization' => 'Basic ' . base64_encode($this->public . ':' . $this->secret),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json'
        ];
    }

    public function pushToIncomingOrders(Order $order)
    {
        $payload = [
            'parcel' => [
                'name' => $order->user->name ?? 'Unknown',
                'company_name' => $order->userCompanyAddress->user_company_name ?? '',
                'address' => $order->userCompanyAddress->company_address1 ?? '',
                'house_number' => '1',
                'city' => $order->userCompanyAddress->company_city ?? '',
                'postal_code' => $order->userCompanyAddress->company_postcode ?? '',
                'country' => strtoupper($order->userCompanyAddress->company_country ?? 'GB'),
                'email' => $order->user->email ?? '',
                'telephone' => $order->user->mobile ?? '',
                'weight' => 0.5, // in kg
                'order_number' => (string) $order->order_id,
                'external_reference' => 'Order-' . $order->order_id,
                'integration' => 524494, // 👈 Your Sendcloud Integration ID
                'request_label' => false
            ]
        ];

        $response = Http::withHeaders($this->authHeaders())
            ->post($this->base . '/parcels', $payload);

        Log::info('Sendcloud Order Push', [
            'payload' => $payload,
            'status' => $response->status(),
            'body' => $response->json()
        ]);

        if ($response->successful()) {
            $data = $response->json()['parcel'];
            $order->update([
                'sendcloud_parcel_id' => $data['id'],
                'cnd_status' => 'created'
            ]);
        } else {
            Log::error('Sendcloud API Error', ['response' => $response->body()]);
        }

        return $response->json();
    }
}
