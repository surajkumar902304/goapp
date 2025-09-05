<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Panel</title>

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="{{ mix('css/app.css') }}">
</head>
<body>
    <div id="customerapp"></div>
<script src="{{ mix('/js/capp.js') }}"></script>
</body>
</html>
