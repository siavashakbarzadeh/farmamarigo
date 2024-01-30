@php
    use Botble\Ecommerce\Models\OffersDetail;
    use Botble\Ecommerce\Models\Offers;
    if (auth('customer')->user() !== null) {
        $userid = auth('customer')->user()->id;
        $pricelist = DB::connection('mysql')->select("select * from ec_pricelist where product_id=$product->id and customer_id=$userid");
        if (isset($pricelist[0])) {
            $reserved_price = $pricelist[0]->final_price;

            $offerDetail = OffersDetail::where('product_id', $product->id)
                ->where('customer_id', $userid)
                ->where('status', 'active')
                ->first();
            if ($offerDetail) {
                $offer = Offers::find($offerDetail->offer_id);
                if ($offer) {
                    $offerType = $offer->offer_type;
                }
            }
        }
    }
@endphp
@if ($product)

    <div class="product-cart-wrap mb-30">
        <div class="product-img-action-wrap">
            <div class="product-img product-img-zoom">
                <a href="{{ $product->url }}">
                    @php
                        $defaultImgUrl = RvMedia::getImageUrl(RvMedia::getDefaultImage());
                        $productImgUrl = RvMedia::getImageUrl($product->images[0]);
                        $ch = curl_init($productImgUrl);
                        curl_setopt($ch, CURLOPT_NOBODY, true);
                        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
                        curl_exec($ch);
                        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        curl_close($ch);

                        if ($responseCode == 200) {
                            $Image = $productImgUrl;
                        } else {
                            $Image = $defaultImgUrl;
                        }
                    @endphp

                    <img class="default-img" src="{{ $Image }}" alt="{{ $product->name }}">

                </a>
                @if (isset($offerDetail))

                    @if ($offerType == 1 || $offerType == 2 || $offerType == 3)
                        <span
                            class="discount-ev">{{ get_sale_percentage($pricelist[0]->final_price, $offerDetail->product_price) }}</span>
                    @elseif ($offerType == 4)
                        <span class="discount-ev">3x2</span>
                    @elseif ($offerType == 5)
                        <span class="discount-ev"><i class="fa fa-link"></i></span>
                    @elseif ($offerType == 6)
                        <span class="discount-ev">QTY</span>
                    @endif

                @endif
            </div>
            <div class="product-badges product-badges-position product-badges-mrg">
                @if ($product->isOutOfStock())
                    <span style="background-color: #000; font-size: 11px;">{{ __('Out Of Stock') }}</span>
                @else
                    @if ($product->productLabels->count())
                        @foreach ($product->productLabels as $label)
                            <span
                                @if ($label->color) style="background-color: {{ $label->color }}" @endif>{{ $label->name }}</span>
                        @endforeach
                    @elseif (
                        $product->front_sale_price !== $product->price &&
                            ($percentSale = get_sale_percentage($product->price, $product->front_sale_price)))
                        <span class="hot">{{ $percentSale }}</span>
                    @endif
                @endif
            </div>

        </div>
        <div class="product-content-wrap">
            @php $category = $product->categories->sortByDesc('id')->first(); @endphp
            @if ($category)
                <div class="product-category">
                    {{--                    <a href="{{ $category->url }}">{{ $category->name }}</a> --}}
                </div>
            @endif
            <h2><a href="{{ $product->url }}">{{ $product->name }}</a></h2>

            @if (EcommerceHelper::isReviewEnabled())
                <div class="rating_wrap">
                    <div class="rating">
                        <div class="product_rate" style="width: {{ $product->reviews_avg * 20 }}%"></div>
                    </div>
                    <span class="rating_num">({{ $product->reviews_count }})</span>
                </div>
            @endif

            <!-- {!! apply_filters('ecommerce_before_product_price_in_listing', null, $product) !!} -->

            <div class="product-price">

                @if (isset($reserved_price))
                    @if (!isset($offerDetail) && $reserved_price !== $product->price)
                        <span>{{ format_price($reserved_price) }}</span>
                        <input type="hidden" name="product_price" class="hidden-product-id"
                            value="{{ $reserved_price }}" />
                        <span class="old-price">{{ format_price($product->price_with_taxes) }}</span>
                    @elseif (isset($offerDetail) &&
                            ($offerType == 1 || $offerType == 2 || $offerType == 3) &&
                            $offerDetail->product_price !== $product->price)
                        <span>{{ format_price($offerDetail->product_price) }}</span>
                        <input type="hidden" name="product_price" class="hidden-product-id"
                            value="{{ $offerDetail->product_price ? $offerDetail->product_price : $pricelist[0]->final_price }}" />
                        <span class="old-price">{{ format_price($product->price_with_taxes) }}</span>
                    @elseif ($offerDetail && ($offerType != 1 || $offerType != 2 || $offerType != 3))
                        <span>{{ format_price($reserved_price) }}</span>
                        <input type="hidden" name="product_price" class="hidden-product-id"
                            value="{{ $reserved_price }}" />
                        <span class="old-price">{{ format_price($product->price_with_taxes) }}</span>
                    @endif
                @else
                    <span>{{ format_price($product->front_sale_price_with_taxes) }}</span>
                @endif


            </div>

            {!! apply_filters('ecommerce_after_product_price_in_listing', null, $product) !!}

            @if (EcommerceHelper::isCartEnabled())
                <div class="product-action-1 show " @if (!EcommerceHelper::isReviewEnabled()) style="bottom: 10px;" @endif>
                    <a aria-label="{{ __('Add To Cart') }}" class="action-btn hover-up add-to-cart-button"
                        data-id="{{ $product->id }}" data-url="{{ route('public.cart.add-to-cart') }}"
                        href="#"><i class="far fa-shopping-bag"></i></a>

                </div>
            @endif
        </div>
    </div>
@endif
