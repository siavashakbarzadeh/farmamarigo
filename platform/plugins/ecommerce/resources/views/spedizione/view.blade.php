@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')

<div class="container">
    <br>
    <h2>Contributo spese di spedizione e imballaggio</h2>
    <br>
    <div class="container">
        <table class="table table-bordered">
            <thead class="bg-white">
            <tr>
                <th>Customer Region</th>
                <th>Customer Type</th>
                <th>Order Amount</th>
                <th>Shipping Costs</th>
            </tr>
            </thead>
            <tbody>
            @foreach($shipping_rules as $rule)
                <tr>
                    <td>{{ $rule['region'] }}</td>
                    <td>{{ implode(', ', $rule['type']) }}</td>
                    <td>{{ $rule['order_amount'] }}</td>
                    <td>{{ $rule['shipping_costs'] }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
    <h2>Shipping Cost</h2>
    {{-- <p>The shipping cost is: {{ $shippingCost }}</p> --}}
    <div class="container mt-5">


    </div>

</div>
@stop

