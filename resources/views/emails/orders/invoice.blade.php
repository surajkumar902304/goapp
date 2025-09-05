@component('mail::message')
# Invoice for Order #TR00{{ $order->order_id }}

**Hi {{ $order->user->name }},**

Thank you for your order.

@component('mail::panel')
**Order Date:** {{ \Carbon\Carbon::parse($order->order_date)->format('F j, Y') }}  
**Total:** £{{ number_format($order->total_paid, 2) }}  
**Status:** {{ ucfirst($order->status) }}
@endcomponent

Thanks,  
{{ config('app.name') }}
@endcomponent
