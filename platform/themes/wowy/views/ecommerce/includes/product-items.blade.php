@php
    use App\Http\Controllers\SuggestionController;
    use Botble\Ecommerce\Models\OffersDetail;
    use Botble\Ecommerce\Models\Offers;
    use Botble\Ecommerce\Models\Product;
    use Botble\Ecommerce\Models\ProductVariation;
    use Botble\Ecommerce\Models\SPC;
    use Botble\Ecommerce\Models\CarouselProducts;
    $layout = theme_option('product_list_layout');

    $requestLayout = request()->input('layout');
    if ($requestLayout && in_array($requestLayout, array_keys(get_product_single_layouts()))) {
        $layout = $requestLayout;
    }

    $layout = $layout && in_array($layout, array_keys(get_product_single_layouts())) ? $layout : 'product-full-width';

    if (request()->user('customer')) {
        $userid = request()->user('customer')->id;
        if (!CarouselProducts::where('customer_id', $userid)->exists()) {
            $discountedProducts = SuggestionController::getProduct($userid);
        } else {
            $productIds = CarouselProducts::where('customer_id', $userid)->pluck('product_id');
            $discountedProducts = Product::whereIn('id', $productIds)->get();
        }
    }
@endphp

<div class="list-content-loading">
    <div class="half-circle-spinner">
        <div class="circle circle-1"></div>
        <div class="circle circle-2"></div>
    </div>
</div>

<div class="shop-product-filter">
    <div class="totall-product">
        <p>{!! BaseHelper::clean(
            __('We found :total items for you!', ['total' => '<strong class="text-brand">' . $products->total() . '</strong>']),
        ) !!}</p>
    </div>
    @include(Theme::getThemeNamespace() . '::views/ecommerce/includes/sort')
</div>

<input type="hidden" name="page" data-value="{{ $products->currentPage() }}">
<input type="hidden" name="sort-by" value="{{ request()->input('sort-by') }}">
<input type="hidden" name="num" value="{{ request()->input('num') }}">

<div class="row">
    <div class="col-12">
        @if (request()->user('customer'))

            <div class="row">
                <center>
                    <h4 class="title-discounted mb-30" style="color:#005BA1; ">
                        <i class="fas fa-circle" style="animation:pulse-blue 2s infinite;border-radius:10px"></i> &nbsp;
                        &nbsp;
                        Pensiamo che questi prodotti potrebbero interessarti
                    </h4>
                </center>
                <div class="owl-carousel owl-theme discounted-carousel ">
                    @foreach ($discountedProducts as $discountedProduct)
                        @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.cart-related-product-items',
                            ['product' => $discountedProduct]
                        )
                    @endforeach
                </div>
            </div>
        @endif
    </div>
    @forelse ($products as $product)
        <div class="col-lg-{{ 12 / ($layout != 'product-full-width' ? 3 : 4) }} col-md-4">
            @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item',
                compact('product'))
        </div>
    @empty
        <div class="container">
            <div class="row mt-4">
                <div class="col-1" style="text-align-last: end">
                    <i class="fa fa-exclamation" style="font-size: xxx-large; color:#e97979"></i>
                </div>
                <div class="col-11">
                    <h3>
                        Nessun prodotto trovato secondo i criteri di ricerca impostati
                    </h3>
                    <h4>
                        Cambiare combinazione di filtri e riprovare.
                    </h4>
                </div>
            </div>
        </div>
    @endforelse
</div>
@if ($products->total() > 0)
    <br>
    {!! $products->withQueryString()->onEachSide(1)->links(Theme::getThemeNamespace() . '::partials.custom-pagination') !!}
@endif
