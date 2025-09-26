<?php

namespace App\Services;

use App\Models\DeliveryMethod;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Models\Order;

class ClickDropService
{
    protected string $base;
    protected string $key;

    public function __construct()
    {
        $this->base = rtrim(config('services.royalmail_cnd.base'), '/');
        $this->key = config('services.royalmail_cnd.key');
    }

    private function authHeaders(): array
    {
        return [
            'Authorization' => 'Bearer ' . $this->key,
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    public function health(): array
    {
        $r = Http::withHeaders($this->authHeaders())->get($this->base . '/version');
        return ['ok' => $r->ok(), 'body' => $r->json(), 'status' => $r->status()];
    }

    private function toKg($w, $unit): float
    {
        $w = (float) ($w ?? 0);
        $u = strtolower((string) ($unit ?: 'g'));
        if ($w <= 0)
            return 0.0;
        return in_array($u, ['g', 'gram', 'grams']) ? round($w / 1000, 3) : round($w, 3);
    }

    private function toGramsFromKg($kg): int
    {
        $g = (int) round(((float) $kg) * 1000);
        return max(1, $g);
    }

    public function mapFromModel(Order $o): array
    {
        $ship = optional($o->userCompanyAddress);
        $usr = optional($o->user);

        $recipient = [
            'companyName' => $ship->user_company_name ?? '',
            'fullName' => $usr->name ?? '',
            'addressLine1' => $ship->company_address1 ?? '',
            'addressLine2' => $ship->company_address2 ?? null,
            'city' => $ship->company_city ?? '',
            'county' => null,
            'postcode' => $ship->company_postcode ?? null,
            'countryCode' => strtoupper((string) ($ship->company_country ?? 'GB')),
        ];
        $billing = [
            'companyName' => $usr->company_name ?? ($ship->user_company_name ?? ''),
            'fullName' => $usr->name ?? '',
            'addressLine1' => $usr->address1 ?? ($ship->company_address1 ?? ''),
            'addressLine2' => $usr->address2 ?? ($ship->company_address2 ?? null),
            'city' => $usr->city ?? ($ship->company_city ?? ''),
            'county' => null,
            'postcode' => $usr->postcode ?? ($ship->company_postcode ?? null),
            'countryCode' => strtoupper((string) ($usr->country ?? $ship->company_country ?? 'GB')),
        ];

        if ($o->items->isEmpty()) {
            throw new \InvalidArgumentException('Order has no items to ship.');
        }

        $contents = [];
        $calcSubtotal = 0.0;
        $totalWeightGrams = 0;

        foreach ($o->items as $it) {
            $v = optional($it->variant);
            $p = optional($v)->product;

            $qty = (int) ($it->quantity ?? 0);
            $price = (float) ($it->unit_price ?? 0);
            $sku = $v->sku ?: ('SKU-' . $it->order_item_id);
            $name = $p->mproduct_title ?? $p->name ?? 'Unknown';

            $unitWeightKg = $this->toKg($v->weight ?? 0, $v->weightunit ?? 'g');
            $unitWeightG = $this->toGramsFromKg(max(0.001, $unitWeightKg));

            $contents[] = [
                'name' => $name,
                'SKU' => $sku,
                'quantity' => $qty,
                'unitValue' => $price,
                'orderTax' => (float) ($o->vat ?? 0),
                'unitWeightInGrams' => $unitWeightG,
                // Optional customs fields (intl):
                // 'customsDescription' => '',
                // 'customsCode'        => '',
                // 'originCountryCode'  => 'GB',
                // 'stockLocation'      => '',
            ];

            $calcSubtotal += $price * $qty;
            $totalWeightGrams += $unitWeightG * $qty;
        }

        $totalWeightGrams = max($totalWeightGrams, 100);

        $dimensions = [
            'heightInMms' => 100,
            'widthInMms' => 200,
            'depthInMms' => 300,
        ];
        $package = [
            'packageFormatIdentifier' => $totalWeightGrams > 2000 ? 'largeParcel'
                : ($totalWeightGrams > 1000 ? 'mediumParcel' : 'smallParcel'),
            'weightInGrams' => $totalWeightGrams,
            'dimensions' => $dimensions,
            'contents' => $contents,
        ];

        return [
            'order_id' => $o->order_id,
            'product_total_amount' => $o->product_total_amount,
            'shipping_fee' => (float) $this->guessShippingFee($o),
            'tax_amount' => (float) ($o->vat ?? 0),
            'total_amount' => (float) $o->total_amount,
            'ship_phone' => $usr->mobile ?? null,
            'ship_email' => $usr->email ?? null,
            'recipient' => $recipient,
            'billing' => $billing,
            'packages' => [$package],
            'serviceCode' => $this->getServiceCode($o),
        ];
    }

    private function guessShippingFee(Order $o): float
    {
        $freeLimit = (float) (Setting::where('key', 'min_order_free_delivery')->value('value') ?? 0);
        if ($o->product_total_amount < $freeLimit) {
            return (float) (DeliveryMethod::where('delivery_method_id', $o->delivery_method_id)
                ->value('delivery_method_amount') ?? 0);
        }
        return 0.0;
    }

    private function getServiceCode(Order $o): string
    {
        $maps = [
            1 => 'RM_48',
            2 => 'RM_24',
        ];
        return $maps[$o->delivery_method_id] ?? 'RM_48';
    }

    public function pushOrder(array $mapped): array
    {
        $addr = $mapped['recipient'] ?? [];
        $miss = [];
        if (empty($addr['addressLine1']))
            $miss[] = 'ship_address1';
        if (empty($addr['city']))
            $miss[] = 'ship_city';
        if (empty($addr['countryCode']))
            $miss[] = 'ship_country_code';
        if (($addr['countryCode'] ?? '') === 'GB' && empty($addr['postcode']))
            $miss[] = 'ship_postcode';

        if ($miss) {
            return [
                'successCount' => 0,
                'errorsCount' => 1,
                'failedOrders' => [
                    [
                        'order' => ['orderReference' => (string) $mapped['order_id']],
                        'errors' => [['errorCode' => 422, 'errorMessage' => 'Missing: ' . implode(', ', $miss)]],
                    ]
                ],
            ];
        }

        $packages = $mapped['packages'] ?? [];
        if (empty($packages) || empty($packages[0]['contents'])) {
            return [
                'successCount' => 0,
                'errorsCount' => 1,
                'failedOrders' => [
                    [
                        'order' => ['orderReference' => (string) $mapped['order_id']],
                        'errors' => [['errorCode' => 422, 'errorMessage' => 'No package contents (products)']],
                    ]
                ],
            ];
        }

        $payload = [
            'items' => [
                [
                    'orderReference' => Str::limit((string) $mapped['order_id'], 40, ''),
                    'orderDate' => now()->toIso8601String(),
                    'subtotal' => (float) $mapped['product_total_amount'],
                    'shippingCostCharged' => (float) ($mapped['shipping_fee'] ?? 0),
                    'orderTax' => (float) ($mapped['tax_amount'] ?? 0),
                    'total' => (float) $mapped['total_amount'],
                    'currencyCode' => 'GBP',

                    'recipient' => [
                        'phoneNumber' => $mapped['ship_phone'] ?? null,
                        'emailAddress' => $mapped['ship_email'] ?? null,
                        'address' => $mapped['recipient'],
                    ],
                    'billing' => [
                        'address' => $mapped['billing'],
                    ],

                    'packages' => $packages,
                ]
            ],
        ];

        Log::info('C&D payload', $payload);

        $r = Http::withHeaders($this->authHeaders())->post($this->base . '/orders', $payload);

        Log::info('C&D response', ['status' => $r->status(), 'body' => $r->body()]);
        return $r->json() ?: ['ok' => false, 'status' => $r->status(), 'body' => $r->body()];
    }
}
