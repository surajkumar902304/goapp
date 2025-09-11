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
            $k = e($k);
            $v = e($v);
            $lines[] = '<strong class="opt-key">' . $k . ':</strong> ' . $v;
        }
        return $lines;
    };

    $brand = 'GOAPP'; 
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>Packing Slips</title>

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
                background: #eee;
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
            box-sizing: border-box;
            background: #fff;
        }

        .page:last-of-type {
            page-break-after: auto;
        }

        .ps-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin: 0 0 12px 0;
        }

        .ps-head__brand {
            font-size: 28px;
            font-weight: 700;
            letter-spacing: .5px;
        }

        .ps-head__meta {
            text-align: right;
            font-size: 14px;
        }

        .ps-addr-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
            margin: 4px 0 20px;
        }

        .ps-addr__title {
            font-weight: 700;
            font-size: 12px;
            letter-spacing: .08em;
            margin-bottom: 4px;
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
        }

        thead {
            display: table-header-group;
        }

        tfoot {
            display: table-row-group;
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
    </style>
</head>

<body>

    @foreach ($orders as $order)
        @php
            $unfulfilledItems = $order->items->filter(function ($it) {
                $fulfilled = (int) ($it->fulfilled_quantity ?? 0);
                $qty = (int) ($it->quantity ?? 0);
                return $qty > $fulfilled;
            });

            $orderDate = $order->order_date ?? $order->created_at;
            $formattedDate = Carbon::parse($orderDate)->format('F d, Y');

            $ship = optional($order->userCompanyAddress);
            $bill = optional($order->userCompanyAddress);

            $shipLine1 = trim(($ship->company_address1 ?? '') . ' ' . ($ship->company_address2 ?? ''));
            $shipLine2 = trim(($ship->company_city ?? '') . ' ' . ($ship->company_country ?? '') . ' ' . ($ship->company_postcode ?? ''));
            $hasShip = ($ship->user_company_name ?? '') !== '' || $shipLine1 !== '' || $shipLine2 !== '';

            $billLine1 = trim(($bill->company_address1 ?? '') . ' ' . ($bill->company_address2 ?? ''));
            $billLine2 = trim(($bill->company_city ?? '') . ' ' . ($bill->company_country ?? '') . ' ' . ($bill->company_postcode ?? ''));
            $hasBill = ($bill->user_company_name ?? '') !== '' || $billLine1 !== '' || $billLine2 !== '';
          @endphp

        @if($unfulfilledItems->count() > 0)
            <div class="page">
                <div class="ps-head">
                    <div class="ps-head__brand">{{ $brand }}</div>
                    <div class="ps-head__meta">
                        <div>Order #TR00{{ $order->order_id }}</div>
                        <div>{{ $formattedDate }}</div>
                    </div>
                </div>

                <div class="ps-addr-row">
                    <div class="ps-addr">
                        <div class="ps-addr__title">SHIP TO</div>
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
                    </div>

                    <div class="ps-addr">
                        <div class="ps-addr__title">BILL TO</div>
                        @if($hasBill)
                            @if($bill->user_company_name)
                            <div>{{ $bill->user_company_name }}</div>@endif
                            @if($billLine1)
                            <div>{{ $billLine1 }}</div>@endif
                            @if($billLine2)
                            <div>{{ $billLine2 }}</div>@endif
                        @else
                            <div class="ps-muted">No billing address</div>
                        @endif
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th align="left">ITEMS</th>
                            <th align="right">QUANTITY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unfulfilledItems as $item)
                            @php
                                $variant = $item->variant;
                                $product = optional($variant)->product;
                                $src = $imgForItem($variant);
                                $remaining = max(0, (int) $item->quantity - (int) $item->fulfilled_quantity);
                              @endphp
                            <tr>
                                <td>
                                    <div class="flex">
                                        <img class="img" src="{{ $src }}" alt="Product">
                                        <div>
                                            <div class="title-strong">{{ optional($product)->mproduct_title }}</div>
                                            <div class="muted">
                                                @foreach($optionLines($variant) as $ln)
                                                    <div class="muted">{!! $ln !!}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td align="right">{{ $remaining }} of {{ (int) $item->quantity }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="footer">
                    <p>Thank you for shopping with us!</p>
                    <p>GOAPP</p>
                    <p>UK</p>
                    <p>info@truewebapp.com</p>
                    <p>truewebapp.com</p>
                </div>
            </div>
        @endif

        @foreach($order->fulfillments as $ful)
            <div class="page">
                <div class="ps-head">
                    <div class="ps-head__brand">{{ $brand }}</div>
                    <div class="ps-head__meta">
                        <div>Order #TR00{{ $order->order_id }}</div>
                        <div>{{ Carbon::parse($ful->fulfilled_at ?? $orderDate)->format('F d, Y') }}</div>
                        @if($ful->tracking_id)
                            <div style="margin-top:4px;">
                                Tracking: {{ $ful->tracking_id }}
                                @if($ful->shipping_courier) &nbsp;&mdash;&nbsp;{{ $ful->shipping_courier }} @endif
                            </div>
                        @endif
                    </div>
                </div>

                <div class="ps-addr-row">
                    <div class="ps-addr">
                        <div class="ps-addr__title">SHIP TO</div>
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
                    </div>

                    <div class="ps-addr">
                        <div class="ps-addr__title">BILL TO</div>
                        @if($hasBill)
                            @if($bill->user_company_name)
                            <div>{{ $bill->user_company_name }}</div>@endif
                            @if($billLine1)
                            <div>{{ $billLine1 }}</div>@endif
                            @if($billLine2)
                            <div>{{ $billLine2 }}</div>@endif
                        @else
                            <div class="ps-muted">No billing address</div>
                        @endif
                    </div>
                </div>

                <table>
                    <thead>
                        <tr>
                            <th align="left">ITEMS</th>
                            <th align="right">QUANTITY</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ful->items as $fi)
                            @php
                                $oi = $fi->orderItem;
                                $variant = optional($oi)->variant;
                                $product = optional($variant)->product;
                                $src = $imgForItem($variant);
                                $qty = (int) $fi->quantity;
                              @endphp
                            <tr>
                                <td>
                                    <div class="flex">
                                        <img class="img" src="{{ $src }}" alt="Product">
                                        <div>
                                            <div class="title-strong">{{ optional($product)->mproduct_title }}</div>
                                            <div class="muted">
                                                @foreach($optionLines($variant) as $ln)
                                                    <div class="muted">{!! $ln !!}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td align="right">{{ $qty }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <div class="footer">
                    <p>Thank you for shopping with us!</p>
                    <p>GOAPP</p>
                    <p>UK</p>
                    <p>info@truewebapp.com</p>
                    <p>truewebapp.com</p>
                </div>
            </div>
        @endforeach

    @endforeach

    <div class="footer no-print">
        <button onclick="window.print()">Print / Save as PDF</button>
    </div>

</body>

</html>