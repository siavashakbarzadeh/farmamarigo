@php
use App\Http\Controllers\PricelistController;
    $products = PricelistController:: pricelist();
@endphp
@if($products !== false)
    <div class="container mb-4">

        <div class="card-deck">
           
            <div class="col-lg-3 col-md-4">
                @foreach($products as $product)
                    <div class="product-cart-wrap mb-30">
                        <div class="product-img-action-wrap">
                            <div class="product-img product-img-zoom">
                                <a href="">
                                    <img src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}" class="default-img">
                                    <img src="{{ RvMedia::getImageUrl($product->image, 'thumb', false, RvMedia::getDefaultImage()) }}" alt="{{ $product->name }}" class="hover-img">
                                </a>
                            </div>
                            <div class="product-badges product-badges-position product-badges-mrg">

                            </div>
                        </div>
                        <div class="product-content-wrap">
                            <div class="product-category">
                                {{--                            <a href="https://marigopharma.marigo.collaudo.biz">infanzia</a>--}}
                            </div>
                            <h2><a href="">{{ $product->name }}</a></h2>
                            <div class="rating_wrap">
                                <div class="rating">
                                    <div class="product_rate" style="width: 0%;">

                                    </div>
                                </div>
                                <span class="rating_num">(0)</span>
                            </div> <div class="product-price">
                                <span>{{ format_price($product->price) }}</span>
                            </div>
                            <div class="product-action-1 show ">
                                <a aria-label="Aggiungi" data-id="18703" data-url="" href="#" class="action-btn hover-up add-to-cart-button"><i class="far fa-shopping-bag"></i></a>
                            </div>
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

