@component('mail::message')
<h1>Welcome to Our Platform</h1>
<p>Hello {{ $name }},</p>
<p>Your account has been created successfully. Below are your login details:</p>
<ul>
    <li>Email: {{ $email }}</li>
    <li>Password: {{ $password }}</li>
</ul>
<p>Please log in and change your password as soon as possible.</p>
<p>Thank you for joining us!</p>

Thanks,<br>
{{ config('app.name') }}
@endcomponent
