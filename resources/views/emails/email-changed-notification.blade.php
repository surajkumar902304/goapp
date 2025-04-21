@component('mail::message')
<h1>Welcome to Our Platform</h1>
<p>Hi {{ $name }},</p>
<p>Your email has been successfully updated.</p>
<ul>
    <li><strong>Old Email:</strong> {{ $oldEmail }}</li>
    <li><strong>New Email:</strong> {{ $newEmail }}</li>
</ul>
<p>If you did not request this change, please contact support immediately.</p>
<p>Thank you for joining us!</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent