@php
use App\Http\Controllers\PricelistController;
    $products = PricelistController:: pricelist();
@endphp
@if($products !== false)
    <div class="container mb-4">

        <div class="card-deck">
            <div class="col-lg-3">
                @foreach($products as $product)
                    <div class="product-item product-loop">
                        <img src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}" class="product-item-thumb">
                        <h3>{{ $product->name }}</h3>
                        <span class="price">
                                    {!! the_product_price($product) !!}
                                </span>
                        <div class="product-action">
                            <a data-quantity='1' data-product='{{ $product->id }}' href="javascript: void(0);"
                               class="btn btn-info">{{ __('Add to cart') }}</a>
                        </div>
                    </div>
                    <div class="card">
                        <div class="product-img-action-wrap">
                            <div class="product-img product-img-zoom">
                                <a href="">

                                    <img class="default-img" src="{{ $product->image }}" alt="Clic Clac Baby">
{{--                                    <img class="hover-img" src="{{ $product->image }}" alt="Clic Clac Baby">--}}
                                </a>
                            </div>


                        </div>
                        {{--                <img class="card-img-top" src="{{ $product->image }}" alt="Card image cap">--}}
                        <div class="card-body">
                            <h5 class="card-title">{{ $product->name }}</h5>

                        </div>

                        <div class="card-footer">
                            <div class="product-price">

                                <span>{{ format_price($product->price) }}</span>



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
{{--    --}}{{-- Handle the case where $products is false --}}
{{--    <p>No data available.</p>    --}}
@endif

