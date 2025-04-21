@component('mail::message')
<h1>Welcome to Our Platform</h1>
<p>Hi {{ $name }},</p>
<p>You have been granted access to a new shop: <strong>{{ $shopName }}</strong>.</p>
<p>Please log in to your account to view or manage this shop.</p>
<p>Thank you for joining us!</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent