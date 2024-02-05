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
                            <th>
                                <div class="container">
                                    <div class="row">
                                        <div class="col-1"></div>
                                        <div class="col-10 row" style="display: flex;
                                        flex-basis: content;margin-left: 28px;
                                        font-size: smaller;">
{{--                                            <div class="col">Modifica</div>--}}
                                            <div class="col">Stampa</div>
                                            <div class="col">Rivedi</div>
                                            <div class="col">Riordina</div>
                                            {{--<div class="col">Riordina</div>--}}
                                        </div>
                                    </div>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($orders as $order)
                            <tr>
                                <td>{{ get_order_code($order->id) }}</td>
{{--                                <td>{{ $order->created_at->format('Y/m/d h:m') }}</td>--}}
                                <td>{{ \Carbon\Carbon::parse($order->created_at)}}</td>
                                <td>{{ __(':price for :total item(s)', ['price' => $order->amount_format, 'total' => $order->products_count]) }}</td>
{{--                                <td>{{ 'price' => $order->amount_format 'total' => $order->products_count }}</td>--}}
<td>
                                    @if( $order->is_finished && $order->is_confirmed)
                                       <label class="btn-success p-1 rounded small" >Completato</label>
                                    @endif
                                    @if( $order->is_finished && !$order->is_confirmed)
                                       <label class="btn-success p-1 rounded small">Mancato Pagamento</label>
                                    @endif
                                    @if( $order->status=='canceled')
                                       <label class="btn-danger p-1 rounded small">Annullato</label>
                                    @endif
                                </td>
                                <td style="display: flex;flex-direction: row;justify-content: center;align-items: center;">



                                    <div class="row list-order-action">
                                        <div class="col-3">
                                            @if($order->isInvoiceAvailable())
                                            <a class='btn btn-primary btn-sm' href="{{ route('customer.print-order', $order->id) }}"
                                               style="color:white !important;width:40px;height:40px;border-radius: 50%;text-align: center;display: flex;flex-direction: row;justify-content: center;align-items: center;">
                                               <i class="fa fa-print"></i>
                                            </a>
                                            @endif
                                            @if($order->payment_id==NULL)

                                            <a class='btn btn-primary btn-sm' href="/checkout/{{ $order->token) }}"
                                                style="color:white !important;width:40px;height:40px;border-radius: 50%;text-align: center;display: flex;flex-direction: row;justify-content: center;align-items: center;">
                                                <i class="fa fa-credit-card"></i>
                                             </a>
                                            @endif

                                        </div>
                                        <div class="col-3">
                                            <a class="btn btn-info btn-sm"  style="color:white !important;width:40px;height:40px;border-radius: 50%;text-align: center;display: flex;flex-direction: row;justify-content: center;align-items: center;" href="{{ route('customer.orders.view', $order->id) }}"><i class="fa fa-eye"></i></a>
                                        </div>
                                        <form action="{{ route('customer.orders.reorder',$order->id) }}" method="post" class="col-3">
                                            @csrf
                                            <button type="submit" class="btn btn-info btn-sm" style="width:40px;height:40px;border-radius: 50%;text-align: center;display: flex;flex-direction: row;justify-content: center;align-items: center;"><i class="fa fa-repeat"></i></button>
                                        </form>
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
