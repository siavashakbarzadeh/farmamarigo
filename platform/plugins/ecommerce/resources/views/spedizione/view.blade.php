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
    <form action="{{ route('admin.ecommerce.spedizione.update') }}" method="post">
        @csrf

         <div class="form-group">
            <label for="min_order">TIPO DI CLIENTE:</label>
             <select name="customertype" class="form-control">
                 <option value="Farmacia">Farmacia</option>
                 <option value="Parafarmacia">Parafarmacia</option>
                 <option value="Altro Pharma">Altro Pharma</option>
                 <option value="Studio Medico">Studio Medico</option>
                 <option value="Dentista">Dentista</option>
             </select>

        </div>
         <div class="form-group">
            <label for="min_order">Ordine Minimo (€):</label>
            <input type="number" step="0.01" name="min_order" id="min_order" class="form-control" value="{{ old('min_order', $spedizione->min_order ?? 350) }}">
        </div>

        <div class="form-group">
            <label for="contribution_lower_order">Contributo per ordine inferiore (€):</label>
            <input type="number" step="0.01" name="contribution_lower_order" id="contribution_lower_order" class="form-control" value="{{ old('contribution_lower_order', $spedizione->contribution_lower_order ?? 29.90) }}">
        </div>


        <div class="form-group">
            <label for="order_300">Ordini ≥ €300 (€):</label>
            <input type="number" step="0.01" name="order_600" id="order_600" class="form-control" value="{{ old('order_600', $spedizione->order_600 ?? 13.90) }}">
        </div>

        <div class="form-group">
            <label for="order_below_300">Ordini < €300 (€):</label>
            <input type="number" step="0.01" name="order_below_600" id="order_below_600" class="form-control" value="{{ old('order_below_600', $spedizione->order_below_600 ?? 18.90) }}">
        </div>

        <div class="form-group">
            <label for="supplement_over_50kg">Per le spedizioni con peso superiore a 50 kg</label>W
            <input type="number" step="0.01" name="supplement_over_50kg" id="supplement_over_50kg" class="form-control" value="{{ old('supplement_over_50kg', $spedizione->supplement_over_50kg ?? 5.00) }}">
        </div>

        <div class="form-group">
            <button type="submit" class="btn btn-primary">Aggiorna</button>
        </div>

    </form>

</div>
@stop

