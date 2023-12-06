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
{{ format_price($product->price) }}
                                </span>
                        <div class="product-action">
                            <a data-quantity='1' data-product='{{ $product->id }}' href="javascript: void(0);"
                               class="btn btn-info">{{ __('Add to cart') }}</a>
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

