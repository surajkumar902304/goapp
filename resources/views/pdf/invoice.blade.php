@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    $cdn = 'https://cdn.truewebpro.com/';

    $imgForItem = function ($variant) use ($cdn) {
        $imageSrc = ($variant->mvariant_image ?? null)
            ?: optional($variant->product)->mproduct_image
            ?: '/images/no-image-available.png';
        return Str::startsWith($imageSrc, ['http', '/images']) ? $imageSrc : $cdn . $imageSrc;
    };

    $optionLines = function ($variant) {
        $detail = optional($variant->details)->first();
        if (!$detail)
            return [];
        $raw = $detail->option_value;
        $options = is_string($raw) ? json_decode($raw, true) : (is_array($raw) ? $raw : []);
        if (!is_array($options))
            return [];

        $lines = [];
        foreach ($options as $k => $v) {
            $lines[] = '<strong class="opt-key">' . e($k) . ':</strong> ' . e($v);
        }
        return $lines;
    };

    $brand = 'GOAPP';
    $orderDate = $order->order_date ?? $order->created_at;
    $formattedDate = Carbon::parse($orderDate)->format('F d, Y');

    $bill = optional($order->userCompanyAddress);
    $ship = optional($order->userCompanyAddress);
    $hasShip = $ship->company_address1 || $ship->company_city || $ship->company_country;

    $billLine1 = trim(($bill->company_address1 ?? '') . ' ' . ($bill->company_address2 ?? ''));
    $billLine2 = trim(($bill->company_city ?? '') . ' ' . ($bill->company_country ?? '') . ' ' . ($bill->company_postcode ?? ''));
    $shipLine1 = trim(($ship->company_address1 ?? '') . ' ' . ($ship->company_address2 ?? ''));
    $shipLine2 = trim(($ship->company_city ?? '') . ' ' . ($ship->company_country ?? '') . ' ' . ($ship->company_postcode ?? ''));

    $orderItems = $order->items;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Invoice - Order #TR00{{ $order->order_id }}</title>
    <style>
        :root {
            --page-margin: 12mm;
            --inner-padding: 8mm;
        }

        @page {
            size: A4;
            margin: var(--page-margin);
        }

        @media screen {
            body {
                margin: 0;
            }

            .page {
                width: 210mm;
                min-height: calc(297mm - 2 * var(--page-margin));
                margin: 12px auto;
                box-shadow: 0 0 0 1px #cfcfcf;
                background: #fff;
            }
        }

        @media print {
            body {
                margin: 0 !important;
            }

            .no-print {
                display: none !important;
            }
        }

        body {
            font-family: Arial, sans-serif;
            color: #000;
        }

        .page {
            page-break-after: always;
            padding: var(--inner-padding);
            background: #fff;
            box-sizing: border-box;
        }

        .page:last-of-type {
            page-break-after: auto;
        }

        .ps-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 12px;
        }

        .ps-head__brand {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .ps-head__meta {
            text-align: right;
        }

        .ps-addr-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .ps-addr__title {
            font-weight: 700;
            font-size: 12px;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .ps-muted {
            color: #777;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 8px;
            border-bottom: 1px solid #e5e5e5;
        }

        thead th {
            border-bottom: 2px solid #000;
            background: #f9f9f9;
        }

        thead {
            display: table-header-group;
        }

        tr,
        img {
            page-break-inside: avoid;
        }

        .flex {
            display: flex;
            align-items: flex-start;
        }

        .muted {
            color: #777;
            font-size: 12px;
        }

        .title-strong {
            font-weight: 700;
            margin-top: 10px;
        }

        .img {
            margin-right: 10px;
            width: 50px;
            height: 50px;
            object-fit: contain;
        }

        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 12px;
        }

        .opt-key {
            color: #000;
            font-weight: 700;
        }

        .invoice-title {
            font-weight: 700;
            font-size: 15px;
        }

        .paid-badge {
            display: inline-block;
            color: #fff;
            font-size: 11px;
            font-weight: bold;
            border-radius: 4px;
            padding: 3px 8px;
            margin-top: 3px;
        }

        .badge-paid {
            background-color: #28a745;
        }

        .badge-pending {
            background-color: #ffc107;
            color: #000;
        }

        .badge-unpaid {
            background-color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="page">
        <table style="margin-bottom: 10px">
            <tbody>
                <tr>
                    <td>
                        <div class="ps-head__brand">{{ $brand }}</div>
                        <div class="ps-addr__title">INVOICE</div>
                        <div class="invoice-title">Invoice / Order No #TR00{{ $order->order_id }}</div>
                        @php
                            $badgeClass = match (strtolower($order->payment_status ?? 'unpaid')) {
                                'paid' => 'badge-paid',
                                'pending' => 'badge-pending',
                                default => 'badge-unpaid'
                            };
                        @endphp
                        <div class="paid-badge {{ $badgeClass }}">{{ strtoupper($order->payment_status ?? 'UNPAID') }}
                        </div>
                    </td>
                    <td style="text-align: right">
                        <div>{{ $seller['name'] ?? 'TrueWeb Pro Limited' }}</div>
                        <div>{{ $seller['address1'] ?? '6 Park Lane, M45 7PB,' }}</div>
                        <div>{{ $seller['address2'] ?? 'Manchester, United Kingdom' }}</div>
                        <div>{{ $seller['email'] ?? 'info@truewebpro.co.uk' }}</div>
                    </td>
                </tr>
            </tbody>
        </table>

        <table style="margin-bottom: 10px">
            <tbody>
                <tr>
                    <td>
                        <div class="ps-addr__title">Billing Details:</div>
                        @if($hasShip)
                            @if($ship->user_company_name)
                            <div>{{ $ship->user_company_name }}</div>@endif
                            @if($shipLine1)
                            <div>{{ $shipLine1 }}</div>@endif
                            @if($shipLine2)
                            <div>{{ $shipLine2 }}</div>@endif
                        @else
                            <div class="ps-muted">No shipping address</div>
                        @endif
                    </td>
                    <td style="text-align: right">
                        <div class="ps-addr__title">Order Info:</div>
                        <p style="margin:0;">
                            Date: {{ Carbon::parse($order->created_at)->format('d M Y, H:i A') }}<br>
                            Order Ref No: {{ $invoice_number ?? ('INV-' . $order->order_id) }}<br>
                            Order Status: {{ ucfirst($order->status) }}<br>
                            Payment Via:
                            {{ $order->pay_by_bank ? 'Bank Transfer' : ucfirst($order->payment_provider ?? 'N/A') }}<br>
                            Payment Status: {{ ucfirst($order->payment_status ?? 'unpaid') }}
                        </p>
                    </td>
                </tr>
            </tbody>
        </table>

        <table>
            <thead>
                <tr>
                    <th align="left">Items</th>
                    <th width="90">Price</th>
                    <th width="50">Qty</th>
                    <th width="100" align="right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orderItems as $item)
                    @php
                        $variant = $item->variant;
                        $product = optional($variant)->product;
                        $src = $imgForItem($variant);
                    @endphp
                    <tr>
                        <td>
                            <div class="flex">
                                <img class="img" src="{{ $src }}" alt="Product">
                                <div>
                                    <div class="title-strong">{{ optional($product)->mproduct_title }}</div>
                                    <div class="muted">
                                        @foreach($optionLines($variant) as $ln)
                                            <div>{!! $ln !!}</div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td>£{{ number_format($item->unit_price, 2) }}</td>
                        <td>{{ (int) $item->quantity }}</td>
                        <td align="right">£{{ number_format($item->unit_price * $item->quantity, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <table class="totals" style="margin-top:15px; width:100%;">
            <tr>
                <td>Subtotal</td>
                <td align="right">£{{ number_format($order->product_total_amount, 2) }}</td>
            </tr>
            <tr>
                <td>Coupon Discount</td>
                <td align="right">- £ {{ number_format(($order->coupon_discount), 2) }}</td>
            </tr>
            <tr>
                <td>Wallet Discount</td>
                <td align="right">- £ {{ number_format(($order->wallet_discount), 2) }}</td>
            </tr>
            <tr>
                <td>Shipping</td>
                <td align="right">{{ ($shippingCost ?? 0) > 0 ? '£ ' . number_format($shippingCost, 2) : 'Free' }}</td>
            </tr>
            <tr>
                <td>Vat {{ $vatPercentage }}%</td>
                <td align="right">£ {{ number_format($order->vat, 2) }}</td>
            </tr>
            <tr style="font-weight:bold; border-top:2px solid #000;">
                <td>Grand Total (Incl Tax)</td>
                <td align="right">£ {{ number_format($order->total_paid, 2) }}</td>
            </tr>
        </table>

        <div class="footer">
            <p>Thank you for shopping with {{ $brand }}!</p>
            <p>{{ $seller['name'] ?? 'TrueWeb Pro Limited' }}</p>
            <p>{{ $seller['email'] ?? 'info@truewebpro.co.uk' }}</p>
        </div>
    </div>
</body>

</html>