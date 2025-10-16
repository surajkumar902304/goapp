<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Your Account Has Been Approved</title>
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
      color: #008060;
      margin: 0;
    }
    .header p {
      color: #777;
      font-size: 14px;
      margin-top: 5px;
    }
    .content p {
      font-size: 15px;
      line-height: 1.6;
      color: #555;
      margin: 10px 0;
    }
    .highlight {
      color: #008060;
      font-weight: bold;
    }
    .btn {
      display: inline-block;
      background-color: #008060;
      color: #fff;
      padding: 12px 22px;
      border-radius: 5px;
      text-decoration: none;
      font-size: 15px;
      margin-top: 20px;
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
      <h1>Account Approved 🎉</h1>
    </div>

    <div class="content">
      <p>Hello <strong>{{ $user->name }}</strong>,</p>
      <p>Your account has been <span class="highlight">approved by the admin</span>.</p>
      <p>You can now log in and start using the platform to explore all available features.</p>

      <p style="margin-top:25px;">If you have any questions, feel free to contact us anytime.</p>
    </div>

    <div class="footer">
      <p>Thanks,</p>
      <p>Need help? Email us at 
        <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
      </p>
    </div>

  </div>
</body>
</html>
