@component('mail::message')
# Hello {{ $user->name }},

Thank you for registering with us. We're **excited to have you on board!**

We look forward to serving you.

If you have any questions or need assistance, feel free to reach out.


Thanks,  
**{{ config('app.name') }} Team**
@endcomponent
