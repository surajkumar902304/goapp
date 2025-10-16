<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>You've been invited by {{ $name }}!</title>
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
      margin: 0;
      color: #008060;
    }
    .header p {
      color: #777;
      font-size: 14px;
      margin: 5px 0 0;
    }
    .content p {
      font-size: 15px;
      line-height: 1.6;
      color: #555;
      margin: 10px 0;
      text-align: center;
    }
    .referral-box {
      background: #f9f9f9;
      border: 2px dashed #008060;
      border-radius: 6px;
      padding: 20px;
      text-align: center;
      margin: 20px auto;
    }
    .referral-code {
      color: #e74c3c;
      font-size: 24px;
      font-weight: bold;
      margin: 10px 0;
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
      <h1>You've Been Invited!</h1>
    </div>

    <div class="content">
      <p><strong>{{ $name }}</strong> has invited you to join <strong>{{ config('app.name') }}</strong>!</p>
      <p>Use the referral code below during signup to get bonus wallet credits:</p>

      <div class="referral-box">
        <div class="referral-code">{{ $referral_code }}</div>
      </div>
    </div>

    <div class="footer">
      <p>If you have any questions, contact us at
        <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.
      </p>
    </div>

  </div>
</body>
</html>
