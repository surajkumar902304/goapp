@component('mail::message')
# Hello {{ $order->user->name }},

We're writing to let you know that your order **#{{ $order->order_id }}** has been **cancelled**.

@component('mail::panel')
**Order Date:** {{ \Carbon\Carbon::parse($order->order_date)->format('F j, Y') }}  
**Total Paid:** £{{ number_format($order->total_paid, 2) }}  
**Status:** Cancelled
@endcomponent

If this was a mistake or you have any questions, feel free to reach out.

Thanks,  
**{{ config('app.name') }} Team**
@endcomponent
