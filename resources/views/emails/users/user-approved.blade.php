@component('mail::message')
# Hello {{ $user->name }},

Your account has been **approved by the admin**.  
You can now log in and start using the platform.


If you have any questions, feel free to contact us.

Thanks,  
**{{ config('app.name') }} Team**
@endcomponent
