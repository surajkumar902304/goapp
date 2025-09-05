@component('mail::message')
# Hello {{ $user->name }},

Your password has been **successfully changed**.

If you did **not** perform this action, please contact our support team immediately to secure your account.


Thanks,  
**{{ config('app.name') }} Team**
@endcomponent
