@php
    $layout = theme_option('product_list_layout');

    $requestLayout = request()->input('layout');
    if ($requestLayout && in_array($requestLayout, array_keys(get_product_single_layouts()))) {
        $layout = $requestLayout;
    }

    $layout = $layout && in_array($layout, array_keys(get_product_single_layouts())) ? $layout : 'product-full-width';
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



<div class="row">
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
