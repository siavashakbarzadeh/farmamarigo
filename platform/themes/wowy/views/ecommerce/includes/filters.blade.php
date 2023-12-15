@php
    Theme::asset()->usePath()
                    ->add('custom-scrollbar-css', 'plugins/mcustom-scrollbar/jquery.mCustomScrollbar.css');
    Theme::asset()->container('footer')->usePath()
                ->add('custom-scrollbar-js', 'plugins/mcustom-scrollbar/jquery.mCustomScrollbar.js', ['jquery']);

    $categories = ProductCategoryHelper::getActiveTreeCategories();

    if (Route::currentRouteName() != 'public.products' && request()->input('categories', [])) {
        $categories = $categories->whereIn('id', (array)request()->input('categories', []));
    }
@endphp

<div class="shop-product-filter-header">
    <div class="row">
        @if (count($categories) > 0)
            <div class="col-lg-12 widget-filter-item">
                <h5 class="mb-20 widget__title" data-title="{{ __('Categories') }}">{{ __('categories') }}</h5>
                <div class="custome-checkbox ps-custom-scrollbar">
                    @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.filter-product-category', ['categories' => $categories, 'indent' => null])
                </div>
            </div>
        @endif

        @php
            $brands = get_all_brands(['status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED], [], ['products']);
        @endphp
{{--        @if (count($brands) > 0)--}}
{{--            <div class="col-lg-12 widget-filter-item">--}}
{{--                <h5 class="mb-20 widget__title" data-title="{{ __('Brand') }}">{{ __('By :name', ['name' => __('Brands')]) }}</h5>--}}
{{--                <div class="custome-checkbox ps-custom-scrollbar">--}}
{{--                    @foreach($brands as $brand)--}}
{{--                        <input class="form-check-input"--}}
{{--                               name="brands[]"--}}
{{--                               type="checkbox"--}}
{{--                               id="brand-filter-{{ $brand->id }}"--}}
{{--                               value="{{ $brand->id }}"--}}
{{--                               @if (in_array($brand->id, request()->input('brands', []))) checked @endif>--}}
{{--                        <label class="form-check-label" for="brand-filter-{{ $brand->id }}"><span class="d-inline-block">{{ $brand->name }}</span> <span class="d-inline-block">({{ $brand->products_count }})</span> </label>--}}
{{--                        <br>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}

        @php
            $tags = app(\Botble\Ecommerce\Repositories\Interfaces\ProductTagInterface::class)->advancedGet([
                'condition' => ['status' => \Botble\Base\Enums\BaseStatusEnum::PUBLISHED],
                'withCount' => ['products'],
                'order_by'  => ['products_count' => 'desc'],
                'take'      => 20,
            ]);
        @endphp
{{--        @if (count($tags) > 0)--}}
{{--            <div class="col-lg-3 col-md-4 mb-lg-0 mb-md-5 mb-sm-5 widget-filter-item">--}}
{{--                <h5 class="mb-20 widget__title" data-title="{{ __('Tag') }}">{{ __('By :name', ['name' => __('tags')]) }}</h5>--}}
{{--                <div class="custome-checkbox">--}}
{{--                    @foreach($tags as $tag)--}}
{{--                        <input class="form-check-input"--}}
{{--                               name="tags[]"--}}
{{--                               type="checkbox"--}}
{{--                               id="tag-filter-{{ $tag->id }}"--}}
{{--                               value="{{ $tag->id }}"--}}
{{--                               @if (in_array($tag->id, request()->input('tags', []))) checked @endif>--}}
{{--                        <label class="form-check-label" for="tag-filter-{{ $tag->id }}"><span class="d-inline-block">{{ $tag->name }}</span> <span class="d-inline-block">({{ $tag->products_count }})</span> </label>--}}
{{--                        <br>--}}
{{--                    @endforeach--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        @endif--}}

        <div class="col-lg-12 widget-filter-item mt-12" data-type="price">
            <h5 class="mb-20 widget__title" data-title="{{ __('Price') }}">{{ __('Price') }}</h5>
            <div class="price-filter range">
                <div class="price-filter-inner">
                    <div class="slider-range"></div>
                    <input type="hidden"
                        class="min_price min-range"
                        name="min_price"
                        value="{{ request()->input('min_price', 0) }}"
                        data-min="0"
                        data-label="{{ __('Min price') }}"/>
                    <input type="hidden"
                        class="min_price max-range"
                        name="max_price"
                           value="{{ request()->input('max_price', (int)theme_option('max_filter_price', 10) * get_current_exchange_rate()) }}"
                           data-max="{{ (int)theme_option('max_filter_price', 10) * get_current_exchange_rate() }}"
                        data-label="{{ __('Max price') }}"/>
                    <div class="price_slider_amount">
                        <div class="label-input">
                            <span class="d-inline-block"> Tra </span>
                            <span class="from d-inline-block"></span>
                            <span class="to d-inline-block"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
{{--            <div class="row">--}}
{{--                {!! render_product_swatches_filter([--}}
{{--                    'view' => Theme::getThemeNamespace() . '::views.ecommerce.attributes.attributes-filter-renderer'--}}
{{--                ]) !!}--}}
{{--            </div>--}}
    </div>

{{--    <a class="show-advanced-filters" href="#">--}}
{{--        <span class="title">{{ __('Advanced filters') }}</span>--}}
{{--        <i class="far fa-angle-up angle-down"></i>--}}
{{--        <i class="far fa-angle-down angle-up"></i>--}}
{{--    </a>--}}

{{--    <div class="advanced-search-widgets" style="display: none">--}}
{{--        <div class="row">--}}
{{--            {!! render_product_swatches_filter([--}}
{{--                'view' => Theme::getThemeNamespace() . '::views.ecommerce.attributes.attributes-filter-renderer'--}}
{{--            ]) !!}--}}
{{--        </div>--}}
{{--    </div>--}}

    <div class="widget mt-3 row">

        <button type="submit" class="btn btn-sm btn-default col-12" style="font-size:small;border-radius:25px; text-transform:none;"><i class="fa fa-filter mr-5 ml-0"></i> {{ __('Filter') }}</button>
    </div>

    <div class="widget mt-3 row">
        <div class="container">
            @php
            $category = $_GET['categories'][0] ?? null;
            @endphp
            <a class="btn btn-sm col-12 clear_filter dib clear_all_filter" style="background-color:white !important;font-size: small; border-radius: 25px; text-transform: none;" href="{{ URL::current() }}">{{ __('Cancella filtri') }}</a>
        </div>
    </div>
</div>
