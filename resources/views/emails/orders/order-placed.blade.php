<div style="max-width:700px;margin:0 auto;font-family:'Helvetica Neue',Helvetica,Arial,sans-serif;color:#333;">

  <h2 style="font-size:22px;font-weight:600;margin-bottom:8px;text-align:right">
    Invoice #TR00{{ $order->order_id }}
  </h2>

  <h3 style="font-size:18px;font-weight:600;margin-bottom:10px;">Order Summary</h3>
  @php
    use Illuminate\Support\Facades\Config;

    $cdn = rtrim(env('AWS_CDN_URL', 'https://cdn.truewebpro.com/'), '/');
  @endphp

  @php
    ob_start();
  @endphp

  <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
    @foreach ($order->items as $item)
      @php
        $variant = optional($item->variant);
        $product = optional($variant->product);
        $cdn = rtrim(env('AWS_CDN_URL', 'https://cdn.truewebpro.com/'), '/');
        $variantImage = $variant->mvariant_image ?? null;
        $productImage = $product->mproduct_image ?? null;
        $image = $variantImage
          ? $cdn . '/' . ltrim($variantImage, '/')
          : ($productImage ? $cdn . '/' . ltrim($productImage, '/') : asset('images/default-product.jpg'));
        $productTitle = $product->mproduct_title ?? $product->product_title ?? 'Product';
      @endphp
      <tr style="border-bottom:1px solid #eee;">
        <td style="padding:10px 0;display:flex;align-items:center;">
          <img src="{{ $image }}" alt="{{ $productTitle }}"
            style="width:60px;height:60px;object-fit:cover;border:1px solid #eee;margin-right:10px;border-radius:4px;">
          <span style="font-size:15px;">{{ $productTitle }} × {{ $item->quantity }}</span>
        </td>
        <td style="text-align:right;white-space:nowrap;font-size:15px;">
          £{{ number_format($item->unit_price ?? $item->price ?? 0, 2) }}
        </td>
      </tr>
    @endforeach
  </table>

  @php
    echo ob_get_clean();
  @endphp


  @php
    $delivery = optional($order->deliveryMethod);
    $ship = optional($order->userCompanyAddress);

    $freeDeliveryLimit = \App\Models\Setting::where('key', 'min_order_free_delivery')->value('value') ?? 0;
    $deliveryCost = 0;

    if ($order->product_total_amount < $freeDeliveryLimit) {
      $deliveryCost = \App\Models\DeliveryMethod::where('delivery_method_id', $delivery->delivery_method_id)
        ->value('delivery_method_amount') ?? 0;
    }
  @endphp

  <table style="width:100%;border-collapse:collapse;font-size:15px;">
    <tr>
      <td style="color:#666;">Subtotal</td>
      <td style="text-align:right;">£{{ number_format($order->product_total_amount ?? 0, 2) }}</td>
    </tr>
    <tr>
      <td style="color:#666;">Shipping</td>
      <td style="text-align:right;">£{{ number_format($deliveryCost, 2) }}</td>
    </tr>
    <tr>
      <td style="color:#666;">Estimated Taxes</td>
      <td style="text-align:right;">£{{ number_format($order->vat ?? 0, 2) }}</td>
    </tr>
    <tr>
      <td style="color:#666;">Discount</td>
      <td style="text-align:right;">- £{{ number_format($order->coupon_discount + $order->wallet_discount ?? 0, 2) }}
      </td>
    </tr>
    <tr style="border-top:2px solid #000;">
      <td style="font-weight:600;padding-top:8px;">Total</td>
      <td style="font-weight:600;padding-top:8px;text-align:right;">
        £{{ number_format($order->total_paid ?? 0, 2) }}
      </td>
    </tr>
  </table>

  <div style="margin-top:35px;">
    <h3 style="font-size:18px;font-weight:600;margin-bottom:10px;">Customer Information</h3>
    <table style="width:100%;border-collapse:collapse;font-size:14px;">
      <tr>
        <td style="width:50%;vertical-align:top;">
          <strong>Shipping Address</strong><br>
          {{ $order->user->name ?? '' }}<br>
          {{ $ship->user_company_name ?? '' }}<br>
          {{ $ship->company_address1 ?? '' }}<br>
          {{ $ship->company_address2 ?? '' }}<br>
          {{ $ship->company_city ?? '' }} {{ $ship->company_postcode ?? '' }}<br>
          {{ strtoupper($ship->company_country ?? 'GB') }}
        </td>
        <td style="width:50%;vertical-align:top;">
          <strong>Billing Address</strong><br>
          {{ $order->user->name ?? '' }}<br>
          {{ $ship->user_company_name ?? '' }}<br>
          {{ $ship->company_address1 ?? '' }}<br>
          {{ $ship->company_address2 ?? '' }}<br>
          {{ $ship->company_city ?? '' }} {{ $ship->company_postcode ?? '' }}<br>
          {{ strtoupper($ship->company_country ?? 'GB') }}
        </td>
      </tr>
    </table>
  </div>

  <div style="margin-top:40px;border-top:1px solid #eee;padding-top:15px;text-align:center;color:#777;font-size:13px;">
    <p>
      If you have any questions, reply to this email or contact us at
      <a href="mailto:{{ config('mail.from.address') }}" style="color:#008060;text-decoration:none;">
        {{ config('mail.from.address') }}
      </a>
    </p>
  </div>

</div>