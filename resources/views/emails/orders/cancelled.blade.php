<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Order #TR00{{ $order->order_id }} Cancelled</title>
  <style>
    body {
      font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
      background-color: #f6f6f6;
      margin: 0;
      padding: 30px;
      color: #333;
    }
    .container {
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
      color: #d93025;
      margin: 0;
    }
    .header p {
      color: #777;
      margin: 5px 0 0;
      font-size: 14px;
    }
    .summary p {
      font-size: 15px;
      margin: 6px 0;
      color: #555;
    }
    .divider {
      border-top: 2px solid #eee;
      margin: 25px 0;
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
    @media only screen and (max-width: 600px) {
      body { padding: 10px; }
      .container { padding: 20px; }
    }
  </style>
</head>

<body>
  <div class="container">

    <div class="header">
      <h1>Order #TR00{{ $order->order_id }} Cancelled</h1>
    </div>

    <p style="font-size:16px;">Hello <strong>{{ $order->user->name }}</strong>,</p>
    <p>We're writing to let you know that your order <strong>#TR00{{ $order->order_id }}</strong> has been <strong style="color:#d93025;">cancelled</strong>.</p>

    <div class="summary">
      <p><strong>Order Date:</strong> {{ \Carbon\Carbon::parse($order->order_date)->format('F j, Y') }}</p>
      <p><strong>Total Paid:</strong> £{{ number_format($order->total_paid, 2) }}</p>
      <p><strong>Status:</strong> Cancelled</p>
    </div>

    <div class="footer">
      <p>If this was a mistake or you have any questions, reply to this email or contact us at
        <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.
      </p>
    </div>

  </div>
</body>
</html>
