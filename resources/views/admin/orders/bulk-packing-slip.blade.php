@php
    use Illuminate\Support\Str;
    use Carbon\Carbon;

    $cdn = 'https://cdn.truewebpro.com/';

    $imgForItem = function($variant) use ($cdn) {
        $imageSrc = ($variant->mvariant_image ?? null)
            ?: optional($variant->product)->mproduct_image
            ?: '/images/no-image-available.png';

        return Str::startsWith($imageSrc, ['http','/images']) ? $imageSrc : $cdn.$imageSrc;
    };

    $optionLines = function($variant) {
        $detail = optional($variant->details)->first();
        if (!$detail) return [];

        $raw = $detail->option_value;
        $options = is_string($raw) ? json_decode($raw, true) : (is_array($raw) ? $raw : []);

        if (!is_array($options)) return [];
        $lines = [];
        foreach ($options as $k => $v) {
            $lines[] = $k.': '.$v;
        }
        return $lines;
    };
@endphp

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Packing Slips</title>
    <style>
        body { font-family: Arial, sans-serif; color:#000; margin: 40px; }
        .header { text-align:center; background:#000; color:#FFD700; padding:20px; }
        .section { margin: 20px 0; }
        .page { page-break-after: always; }
        @media print {
            .no-print { display:none; }
            .page:last-of-type { page-break-after: auto; }
            .header { margin: 20px; padding: 20px; }
            .section { margin: 20px; padding: 20px; }
            body { margin: 0 !important; }
        }
        table { width:100%; border-collapse: collapse; }
        th, td { padding: 8px; border-bottom: 1px solid #e5e5e5; }
        thead th { border-bottom: 2px solid #000; }
        .flex { display:flex; align-items:flex-start; }
        .space-between { display:flex; justify-content:space-between; }
        .muted { color:#777; font-size:12px; }
        .title-strong { font-weight: bold; margin-top: 10px; }
        .footer { margin-top: 40px; text-align:center; font-size:12px; }
        .img { margin-right:10px; width:50px; height:50px; object-fit:contain; }
    </style>
</head>
<body>

@foreach ($orders as $order)
    @php
        $unfulfilledItems = $order->items->filter(function ($it) {
            $fulfilled = (int)($it->fulfilled_quantity ?? 0);
            $qty       = (int)($it->quantity ?? 0);
            return $qty > $fulfilled;
        });

        $orderDate     = $order->order_date ?? $order->created_at;
        $formattedDate = Carbon::parse($orderDate)->format('F d, Y');
    @endphp

    {{-- Unfulfilled --}}
    @if($unfulfilledItems->count() > 0)
    <div class="page">
        <div class="header">
            <h1>GOAPP Packing Slip</h1>
            <p>Order #{{ $order->order_id }}<br>{{ $formattedDate }}</p>
        </div>

        <div class="section space-between">
            <div style="width:48%;">
                <h3>Customer Info</h3>
                <p>{{ $order->user->name }}</p>
                <p>{{ $order->user->email }}</p>
                <p>{{ $order->user->mobile }}</p>
            </div>
            <div style="width:48%; text-align:right;">
                <h3>Shipping Address</h3>
                <p>{{ optional($order->userCompanyAddress)->user_company_name }}</p>
                <p>
                    {{ trim((optional($order->userCompanyAddress)->company_address1 ?? '').' '.(optional($order->userCompanyAddress)->company_address2 ?? '')) }}
                </p>
                <p>
                    {{ trim((optional($order->userCompanyAddress)->company_city ?? '').' '.(optional($order->userCompanyAddress)->company_country ?? '').' '.(optional($order->userCompanyAddress)->company_postcode ?? '')) }}
                </p>
            </div>
        </div>

        <div class="section">
            <table>
                <thead>
                    <tr>
                        <th align="left">ITEMS</th>
                        <th align="center"></th>
                        <th align="right">QUANTITY</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($unfulfilledItems as $item)
                        @php
                            $variant   = $item->variant;
                            $product   = optional($variant)->product;
                            $src       = $imgForItem($variant);
                            $remaining = max(0, (int)$item->quantity - (int)$item->fulfilled_quantity);
                        @endphp
                        <tr>
                            <td>
                                <div class="flex">
                                    <img class="img" src="{{ $src }}" alt="Product">
                                    <div>
                                        <div class="title-strong">{{ optional($product)->mproduct_title }}</div>
                                        <div class="muted">{{ $variant->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach($optionLines($variant) as $ln)
                                    <div class="muted">{{ $ln }}</div>
                                @endforeach
                            </td>
                            <td align="right">
                                {{ $remaining }} of {{ (int)$item->quantity }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section" style="text-align:center">
            <p>Thank you for shopping with us!</p>
            <p>GOAPP</p>
            <p>UK</p>
            <p>info@truewebapp.com</p>
            <p>truewebapp.com</p>
        </div>
    </div>
    @endif

    {{-- Fulfillments --}}
    @foreach($order->fulfillments as $ful)
    <div class="page">
        <div class="header">
            <h1>GOAPP Packing Slip</h1>
            <p>
                Order #{{ $order->order_id }}<br>
                {{ Carbon::parse($ful->fulfilled_at ?? $orderDate)->format('F d, Y') }}
                @if($ful->tracking_id)
                    <br>
                    Tracking: {{ $ful->tracking_id }}
                    @if($ful->shipping_courier)
                        &nbsp;&mdash;&nbsp;{{ $ful->shipping_courier }}
                    @endif
                @endif
            </p>
        </div>

        <div class="section space-between">
            <div style="width:48%;">
                <h3>Customer Info</h3>
                <p>{{ $order->user->name }}</p>
                <p>{{ $order->user->email }}</p>
                <p>{{ $order->user->mobile }}</p>
            </div>
            <div style="width:48%; text-align:right;">
                <h3>Shipping Address</h3>
                <p>{{ optional($order->userCompanyAddress)->user_company_name }}</p>
                <p>
                    {{ trim((optional($order->userCompanyAddress)->company_address1 ?? '').' '.(optional($order->userCompanyAddress)->company_address2 ?? '')) }}
                </p>
                <p>
                    {{ trim((optional($order->userCompanyAddress)->company_city ?? '').' '.(optional($order->userCompanyAddress)->company_country ?? '').' '.(optional($order->userCompanyAddress)->company_postcode ?? '')) }}
                </p>
            </div>
        </div>

        <div class="section">
            <table>
                <thead>
                    <tr>
                        <th align="left">ITEMS</th>
                        <th align="center"></th>
                        <th align="right">QUANTITY</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($ful->items as $fi)
                        @php
                            $oi      = $fi->orderItem;                
                            $variant = optional($oi)->variant;
                            $product = optional($variant)->product;
                            $src     = $imgForItem($variant);
                            $qty     = (int)$fi->quantity;           
                        @endphp
                        <tr>
                            <td>
                                <div class="flex">
                                    <img class="img" src="{{ $src }}" alt="Product">
                                    <div>
                                        <div class="title-strong">{{ optional($product)->mproduct_title }}</div>
                                        <div class="muted">{{ optional($variant)->sku }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @foreach($optionLines($variant) as $ln)
                                    <div class="muted">{{ $ln }}</div>
                                @endforeach
                            </td>
                            <td align="right">{{ $qty }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="section" style="text-align:center">
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
