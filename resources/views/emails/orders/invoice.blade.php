<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Invoice #TR00{{ $order->order_id }}</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      background-color: #f6f6f6;
      margin: 0;
      padding: 30px;
      color: #333;
    }
    .invoice-container {
      max-width: 700px;
      margin: 0 auto;
      background: #fff;
      border-radius: 8px;
      padding: 40px;
      box-shadow: 0 0 10px rgba(0,0,0,0.05);
    }
    .header {
      text-align: center;
      margin-bottom: 30px;
    }
    .header h1 {
      font-size: 22px;
      margin: 0;
      color: #333;
    }
    .header p {
      color: #777;
      font-size: 14px;
      margin: 5px 0 0;
    }
    .summary {
      margin-bottom: 25px;
    }
    .summary p {
      font-size: 15px;
      margin: 4px 0;
      color: #555;
    }
    .summary strong {
      color: #000;
    }
    .divider {
      border-top: 2px solid #eee;
      margin: 25px 0;
    }
    .total {
      text-align: right;
      font-weight: bold;
      font-size: 18px;
    }
    .footer {
      text-align: center;
      border-top: 1px solid #eee;
      margin-top: 30px;
      padding-top: 20px;
      font-size: 13px;
      color: #777;
    }
    .footer a {
      color: #008060;
      text-decoration: none;
    }
    .btn {
      display: inline-block;
      background-color: #008060;
      color: #fff;
      padding: 12px 22px;
      border-radius: 5px;
      text-decoration: none;
      font-size: 15px;
      margin-top: 15px;
    }
    .btn:hover {
      background-color: #00684a;
    }
    @media only screen and (max-width: 600px) {
      body { padding: 10px; }
      .invoice-container { padding: 20px; }
    }
  </style>
</head>

<body>
  <div class="invoice-container">

    <div class="header">
      <h1>Invoice #TR00{{ $order->order_id }}</h1>
    </div>

    <p style="font-size:16px;">Hi <strong>{{ $order->user->name }}</strong>,</p>
    <p>Thank you for your order. Below is your invoice summary:</p>

    <div class="summary">
      <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('F j, Y') }}</p>
      <p><strong>Status:</strong> {{ ucfirst($order->status) }}</p>
      <p><strong>Total Paid:</strong> £{{ number_format($order->total_paid, 2) }}</p>
    </div>

    <div class="divider"></div>

    @php
      $ship = optional($order->userCompanyAddress);
    @endphp

    <div class="summary">
      <h3 style="font-size:17px; font-weight:600; margin-bottom:10px;">Billing Information</h3>
      <p>{{ $order->user->name }}</p>
      <p>{{ $ship->user_company_name ?? '' }}</p>
      <p>{{ $ship->company_address1 ?? '' }} {{ $ship->company_address2 ?? '' }}</p>
      <p>{{ $ship->company_city ?? '' }} {{ $ship->company_postcode ?? '' }}</p>
      <p>{{ strtoupper($ship->company_country ?? 'GB') }}</p>
    </div>

    <div class="divider"></div>

    <div class="footer">
      <p>If you have any questions, contact us at
        <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
      </p>
      <p>Thank you for choosing us!</p>
    </div>

  </div>
</body>
</html>
