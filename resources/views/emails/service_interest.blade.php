<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Service Interest Submission</title>
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
    .section h2 {
      font-size: 18px;
      color: #008060;
      border-bottom: 2px solid #eee;
      padding-bottom: 6px;
      margin-bottom: 15px;
    }
    p {
      font-size: 15px;
      line-height: 1.6;
      color: #555;
      margin: 8px 0;
    }
    strong {
      color: #000;
    }
    .image {
      margin-top: 15px;
      text-align: center;
    }
    .image img {
      max-width: 100%;
      border-radius: 6px;
      border: 1px solid #ddd;
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
      <h1>Service Interest Submission</h1>
    </div>

    <div class="section">
      <h2>Submitted By</h2>
      <p><strong>Name:</strong> {{ $data['name'] ?? '—' }}</p>
      <p><strong>Phone:</strong> {{ $data['phone'] ?? '—' }}</p>
      <p><strong>Email:</strong> {{ $data['email'] ?? '—' }}</p>
      <p><strong>Note:</strong> {{ $data['note'] ?? '—' }}</p>
    </div>

    <div style="border-top:1px solid #eee; margin:25px 0;"></div>

    <div class="section">
      <h2>Service Details</h2>
      <p><strong>Title:</strong> {{ $data['service_title'] ?? '—' }}</p>
      <p><strong>Subtitle:</strong> {{ $data['service_subtitle'] ?? '—' }}</p>
      <p><strong>Description:</strong> {{ $data['service_desc'] ?? '—' }}</p>

      @if(!empty($data['service_image']))
        <div class="image">
          <p><strong>Image:</strong></p>
          <img src="{{ $data['service_image'] }}" alt="Service Image">
        </div>
      @endif
    </div>

    <div class="footer">
      <p>This message was generated automatically.</p>
      <p>For any questions, contact us at 
        <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>.
      </p>
    </div>

  </div>
</body>
</html>
