@component('mail::message')
# Hi {{ $order->user->name }},

Thank you for your order! 🎉

@component('mail::panel')
**Order ID:** #TR00{{ $order->order_id }}  
**Order Date:** {{ \Carbon\Carbon::parse($order->order_date)->format('F j, Y \a\t g:i A') }}  
**Total Paid:** £{{ number_format($order->total_paid, 2) }}  
**Payment Status:** {{ ucfirst($order->status) }}
@endcomponent

We’re preparing your order and will notify you once it ships.  
If you have any questions, feel free to contact us.

Thanks again,  
**{{ config('app.name') }} Team**
@endcomponent
