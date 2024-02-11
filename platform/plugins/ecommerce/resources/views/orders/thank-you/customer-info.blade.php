@php
    use Botble\Payment\Models\Payment;
    if (!isset($payment)) {
        $payment = Payment::where('order_id', $order->id)->first();
        $paymentStatus = $payment->status;
        $paymentChannel = $payment->payment_channel;
    } else {
        $paymentStatus = $payment->status;
        $paymentChannel = $payment->payment_channel;
    }

@endphp
<div class="order-customer-info">
    <h3> {{ __('Customer information') }}</h3>
    <p>
        <span class="d-inline-block">{{ __('Payment method') }}:</span>
        <span class="order-customer-info-meta">{{ $paymentChannel }}</span>
    </p>
    <p>
        <span class="d-inline-block">{{ __('Payment status') }}:</span>
        <span class="order-customer-info-meta" style="text-transform: uppercase">{!! $paymentStatus !!}</span>
    </p>
</div>
