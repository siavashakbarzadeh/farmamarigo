@php
use App\Http\Controllers\PricelistController;
    $lists = PricelistController:: pricelist();
@endphp
@if($lists !== false)
    <div class="container">

        <div class="card-deck">
            <div class="col-lg-3">
                @foreach($lists as $list)
                    <div class="card">
                        {{--                <img class="card-img-top" src="{{ $list->image }}" alt="Card image cap">--}}
                        <div class="card-body">
                            <h5 class="card-title">{{ $list->name }}</h5>
                                                
                        </div>
                        <div class="product-price">
                            <span>{{ $list->price }}</span>



                        </div>
                        <div class="card-footer">
                            <small class="text-muted">Last updated 3 mins ago</small>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
{{--@else--}}
{{--    --}}{{-- Handle the case where $lists is false --}}
{{--    <p>No data available.</p>    --}}
@endif

