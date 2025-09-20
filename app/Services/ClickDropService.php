<?php

namespace App\Services;

use App\Models\DeliveryMethod;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
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

    public function mapFromModel(Order $o): array
    {
        $ship = optional($o->userCompanyAddress);  
        $usr = optional($o->user);

        $ship_company = $ship->user_company_name ?? null;
        $ship_full = $usr->name ?? null;
        $ship_addr1 = $ship->company_address1 ?? null;
        $ship_addr2 = $ship->company_address2 ?? null;
        $ship_city = $ship->company_city ?? null;
        $ship_post = $ship->company_postcode ?? null;
        $ship_ccode = strtoupper((string) ($ship->company_country ?? 'GB'));
        $ship_phone = $usr->mobile ?? null;
        $ship_email = $usr->email ?? null;

        $bill_company = $usr->company_name ?? null;
        $bill_full = $usr->name ?? null;
        $bill_addr1 = $usr->address1 ?? null;
        $bill_addr2 = $usr->address2 ?? null;
        $bill_city = $usr->city ?? null;
        $bill_post = $usr->postcode ?? null;
        $bill_ccode = strtoupper((string) ($usr->country ?? 'GB'));

        return [
            'order_id' => $o->order_id,
            'product_total_amount' => (float) $o->product_total_amount,
            'shipping_fee' => (float) $this->guessShippingFee($o),
            'tax_amount' => (float) $o->vat ?? 0,
            'total_amount' => (float) $o->total_amount,

            'ship_company_name' => $ship_company,
            'ship_full_name' => $ship_full,
            'ship_phone' => $ship_phone,
            'ship_email' => $ship_email,
            'ship_address1' => $ship_addr1,
            'ship_address2' => $ship_addr2,
            'ship_city' => $ship_city,
            'ship_post_town' => $ship_city,    
            'ship_postcode' => $ship_post,
            'ship_country_code' => $ship_ccode,

            'bill_company_name' => $bill_company,
            'bill_full_name' => $bill_full,
            'bill_address1' => $bill_addr1,
            'bill_address2' => $bill_addr2,
            'bill_city' => $bill_city,
            'bill_postcode' => $bill_post,
            'bill_country_code' => $bill_ccode,
        ];
    }


    private function guessShippingFee(Order $o): float
    {
        $freeLimit = (float) (Setting::where('key', 'min_order_free_delivery')->value('value') ?? 0);
        if ($o->product_total_amount < $freeLimit) {
            return (float) (DeliveryMethod::where('delivery_method_id', $o->delivery_method_id)->value('delivery_method_amount') ?? 0);
        }
        return 0.0;
    }

    private function validateForRoyalMail(array $order): array
    {
        $missing = [];
        if (empty($order['company_name']) && empty($order['full_name']))
            $missing[] = 'company_name|full_name (one required)';
        if (empty($order['address1']))
            $missing[] = 'address1';
        if (empty($order['city']) && empty($order['post_town']))
            $missing[] = 'city|post_town (one required)';
        if (empty($order['country_code']))
            $missing[] = 'country_code';
        // Postcode is strongly recommended for GB; make it required if your business rules say so
        // if ($order['country_code'] === 'GB' && empty($order['postcode'])) $missing[] = 'postcode';

        return $missing;
    }

    public function pushOrder(array $order): array
    {
        $missing = [];
        if (empty($order['ship_company_name']) && empty($order['ship_full_name']))
            $missing[] = 'ship_company_name|ship_full_name';
        if (empty($order['ship_address1']))
            $missing[] = 'ship_address1';
        if (empty($order['ship_city']))
            $missing[] = 'ship_city';
        if (empty($order['ship_country_code']))
            $missing[] = 'ship_country_code';
        if (($order['ship_country_code'] ?? '') === 'GB' && empty($order['ship_postcode']))
            $missing[] = 'ship_postcode';
        if ($missing) {
            return [
                'successCount' => 0,
                'errorsCount' => 1,
                'failedOrders' => [
                    [
                        'order' => ['orderReference' => (string) $order['order_id']],
                        'errors' => [
                            [
                                'errorCode' => 422,
                                'errorMessage' => 'Missing required fields (shipping): ' . implode(', ', $missing),
                            ]
                        ]
                    ]
                ]
            ];
        }

        $recipientAddress = [
            'companyName' => $order['ship_company_name'] ?? '',
            'fullName' => $order['ship_full_name'] ?? '',
            'addressLine1' => $order['ship_address1'] ?? null,
            'addressLine2' => $order['ship_address2'] ?? null,
            'postTown' => $order['ship_post_town'] ?? $order['ship_city'] ?? null,
            'city' => $order['ship_city'] ?? null,
            'postcode' => $order['ship_postcode'] ?? null,
            'countryCode' => $order['ship_country_code'] ?? 'GB',
        ];

        $billingAddress = [
            'companyName' => $order['bill_company_name'] ?? $order['ship_company_name'] ?? '',
            'fullName' => $order['bill_full_name'] ?? $order['ship_full_name'] ?? '',
            'addressLine1' => $order['bill_address1'] ?? $order['ship_address1'] ?? null,
            'addressLine2' => $order['bill_address2'] ?? $order['ship_address2'] ?? null,
            'postTown' => $order['bill_city'] ?? $order['ship_city'] ?? null, // RM uses postTown/city
            'city' => $order['bill_city'] ?? $order['ship_city'] ?? null,
            'postcode' => $order['bill_postcode'] ?? $order['ship_postcode'] ?? null,
            'countryCode' => $order['bill_country_code'] ?? $order['ship_country_code'] ?? 'GB',
        ];

        $billMissing = [];
        if (empty($billingAddress['companyName']) && empty($billingAddress['fullName']))
            $billMissing[] = 'billing company/full name';
        if (empty($billingAddress['addressLine1']))
            $billMissing[] = 'billing addressLine1';
        if (empty($billingAddress['city']))
            $billMissing[] = 'billing city';
        if (($billingAddress['countryCode'] ?? '') === 'GB' && empty($billingAddress['postcode']))
            $billMissing[] = 'billing postcode';
        if ($billMissing) {
            // We *could* automatically mirror shipping here, but we already did via fallback.
            // If still missing, return a friendly error:
            return [
                'successCount' => 0,
                'errorsCount' => 1,
                'failedOrders' => [
                    [
                        'order' => ['orderReference' => (string) $order['order_id']],
                        'errors' => [
                            [
                                'errorCode' => 422,
                                'errorMessage' => 'Missing required fields (billing): ' . implode(', ', $billMissing),
                            ]
                        ]
                    ]
                ]
            ];
        }

        $payload = [
            'items' => [
                [
                    'orderReference' => Str::limit((string) $order['order_id'], 40, ''),
                    'orderDate' => now()->toIso8601String(),
                    'subtotal' => (float) $order['product_total_amount'],
                    'shippingCostCharged' => (float) ($order['shipping_fee'] ?? 0),
                    'orderTax' => (float) $order['tax_amount'] ?? 0,
                    'total' => (float) $order['total_amount'],
                    'currencyCode' => 'GBP',
                    'recipient' => [
                        'phoneNumber' => $order['ship_phone'] ?? $order['phone'] ?? null,
                        'emailAddress' => $order['ship_email'] ?? $order['email'] ?? null,
                        'address' => $recipientAddress,
                    ],
                    'billing' => [
                        'address' => $billingAddress
                    ],
                ]
            ]
        ];

        $r = Http::withHeaders($this->authHeaders())->post($this->base . '/orders', $payload);
        return $r->json() ?: ['ok' => false, 'status' => $r->status(), 'body' => $r->body()];
    }


    public function pushMany(array $ordersPayload): array
    {
        // optionally validate each and split good/bad
        $items = [];
        foreach ($ordersPayload as $order) {
            if ($this->validateForRoyalMail($order)) {
                continue;
            }
            $items[] = [
                'orderReference' => Str::limit((string) $order['order_id'], 40, ''),
                'orderDate' => now()->toIso8601String(),
                'subtotal' => (float) $order['product_total_amount'],
                'shippingCostCharged' => (float) ($order['shipping_fee'] ?? 0),
                'orderTax'          => (float) $order['tax_amount'] ?? 0,
                'total' => (float) $order['total_amount'],
                'currencyCode' => 'GBP',
                'recipient' => [
                    'phoneNumber' => $order['phone'] ?? null,
                    'emailAddress' => $order['email'] ?? null,
                    'address' => [
                        'companyName' => $order['company_name'] ?? '',
                        'fullName' => $order['full_name'] ?? '',

                        'addressLine1' => $order['address1'] ?? null,
                        'addressLine2' => $order['address2'] ?? null,

                        'postTown' => $order['post_town'] ?? null,
                        'city' => $order['city'] ?? null,

                        'postcode' => $order['postcode'] ?? null,
                        'countryCode' => $order['country_code'] ?? 'GB',
                    ],
                ],
            ];
        }

        if (empty($items)) {
            return ['successCount' => 0, 'errorsCount' => 1, 'failedOrders' => [['errors' => [['errorMessage' => 'No valid items to push']]]]];
        }

        $payload = ['items' => array_values($items)];
        $r = Http::withHeaders($this->authHeaders())->post($this->base . '/orders', $payload);
        return $r->json() ?: ['ok' => false, 'status' => $r->status(), 'body' => $r->body()];
    }
}
