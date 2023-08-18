@extends(Theme::getThemeNamespace() . '::views.ecommerce.customers.master')
@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ __('Your Orders') }}</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>{{ __('ID number') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th>{{ __('Total') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ get_order_code($order->id) }}</td>
                                <td>{{ $order->created_at->format('Y/m/d h:m') }}</td>
                                <td>{{ __(':price for :total item(s)', ['price' => $order->amount_format, 'total' => $order->products_count]) }}</td>
                                <td>
                                    @if( $order->status=='completed' & $order->shipment->status =='delivering')

                                        <lable>Spedito</lable>

                                    @endif
                                    @if( $order->status=='pending')

                                        <lable>Modificabile</lable>

                                    @endif
                                    @if( $order->status=='completed' && $order->shipment->status =='pending')

                                        <lable>In lavorazione</lable>

                                    @endif
                                    @if( $order->status=='canceled')

                                        <lable>Cancellato</lable>

                                    @endif


                                </td>
                                <td style="display: flex;flex-direction: row;justify-content: center;align-items: center;">




                                    <div class="row list-order-action">
                                        {{--<div class="col-4">
                                            <form  @if($order->canEdit()) action="{{ route('customer.orders.edit',$order->id) }}" method="post" @endif >
                                                @csrf
                                                <button type="submit" class="btn btn-success btn-sm" @if( $order->status !='pending') disabled @endif> <i class="fa fa-pen"></i> </button>
                                            </form>
                                        </div>--}}
                                        <div class="col-4">
                                            <a @if($order->isInvoiceAvailable()) href="{{ route('customer.print-order', $order->id) }}" @endif
                                            class="btn btn-primary btn-sm" @if(!$order->isInvoiceAvailable()) disabled @endif><i class="fa fa-print"></i></a>
                                        </div>
                                        <div class="col-4">
                                            <a class="btn btn-info btn-sm" href="{{ route('customer.orders.view', $order->id) }}"><i class="fa fa-eye"></i></a>
                                        </div>
                                    </div>


                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-center" colspan="5">{{ __('No orders found!') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                {!! $orders->links(Theme::getThemeNamespace() . '::partials.custom-pagination') !!}
            </div>
        </div>
    </div>
@endsection
