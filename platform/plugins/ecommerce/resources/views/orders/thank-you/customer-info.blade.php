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
    @if ($order->address->id)
        @if ($order->address->name)
            <p>
                <span class="d-inline-block">{{ __('Full name') }}:</span>
                <span class="order-customer-info-meta">{{ $order->address->name }}</span>
            </p>
        @endif

        @if ($order->address->phone)
            <p>
                <span class="d-inline-block">{{ __('Phone') }}:</span>
                <span class="order-customer-info-meta">{{ $order->address->phone }}</span>
            </p>
        @endif

        @if ($order->address->email)
            <p>
                <span class="d-inline-block">{{ __('Email') }}:</span>
                <span class="order-customer-info-meta">{{ $order->address->email }}</span>
            </p>
        @endif

        @if ($order->full_address)
            <p>
                <span class="d-inline-block">{{ __('Address') }}:</span>
                <span class="order-customer-info-meta">{{ $order->full_address }}</span>
            </p>
        @endif
    @endif

    @if (!empty($isShowShipping))
        <p>
            <span class="d-inline-block">{{ __('Shipping method') }}:</span>
            <span class="order-customer-info-meta">{{ $order->shipping_method_name }} -
                {{ format_price($order->shipping_amount) }}</span>
        </p>
    @endif
    <p>

        <span class="d-inline-block">{{ __('Payment method') }}:</span>
        <span class="order-customer-info-meta">{{ $paymentChannel }}</span>
    </p>
    <p>
        <span class="d-inline-block">{{ __('Payment status') }}:</span>
        <span class="order-customer-info-meta" style="text-transform: uppercase">{!! $paymentStatus !!}</span>
    </p>
</div>
