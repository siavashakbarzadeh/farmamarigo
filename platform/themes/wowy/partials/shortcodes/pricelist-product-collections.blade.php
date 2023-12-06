@php
use App\Http\Controllers\PricelistController;
    $lists = PricelistController:: pricelist();
@endphp
@if($lists !== false)
    <div class="container mb-4">

        <div class="card-deck">
            <div class="col-lg-3">
                @foreach($lists as $list)
                    <div class="card">
                        <div class="product-img-action-wrap">
                            <div class="product-img product-img-zoom">
                                <a href="">

                                    <img class="default-img" src="{{ $list->image }}" alt="Clic Clac Baby">
{{--                                    <img class="hover-img" src="{{ $list->image }}" alt="Clic Clac Baby">--}}
                                </a>
                            </div>


                        </div>
                        {{--                <img class="card-img-top" src="{{ $list->image }}" alt="Card image cap">--}}
                        <div class="card-body">
                            <h5 class="card-title">{{ $list->name }}</h5>

                        </div>

                        <div class="card-footer">
                            <div class="product-price">

                                <span>{{ format_price($list->price) }}</span>



                            </div>
                            <button id="btn-add-cart" class="btn btn-lg btn-black"><i class="fa fa-shopping-bag"
                                                                                      aria-hidden="true"></i>{{ __('Add to cart') }}
                            </button>
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

