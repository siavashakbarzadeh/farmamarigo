@php
use App\Http\Controllers\PricelistController;
    $lists = PricelistController:: pricelist();
@endphp
{{--@if (auth('customer'))--}}
{{--<section class="product-tabs pt-40 pb-30 wow fadeIn animated">--}}
{{--    <product-collections-component title="{!! BaseHelper::clean($title) !!}"--}}
{{-- :product_collections="{{ json_encode($productCollections) }}" url="{{ route('public.ajax.products') }}"></product-collections-component>--}}
{{--</section>--}}
{{--@endif--}}
<div class="container">
    <div class="card-deck">
        @foreach($lists as $list)
            <div class="card">
{{--                <img class="card-img-top" src="{{ $list->image }}" alt="Card image cap">--}}
                <div class="card-body">
                    <h5 class="card-title">{{ $list->name }}</h5>
{{--                    <p class="card-text">{{ $list->description }}</p>--}}
                </div>
                <div class="card-footer">
                    <small class="text-muted">Last updated 3 mins ago</small>
                </div>
            </div>
        @endforeach
    </div>
</div>
