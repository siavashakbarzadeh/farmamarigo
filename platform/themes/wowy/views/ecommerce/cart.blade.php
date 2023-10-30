<section class="mt-60 mb-60">
    <div class="container">
        <div class="row">
            <div class="col-12 section--shopping-cart">
                <form class="form--shopping-cart" method="post" action="{{ route('public.cart.update') }}">
                    @csrf
                    <div class="row">
                        <div class="col-8">
                            @if (count($products) > 0)
                                <div class="table-responsive">
                                    <table class="table shopping-summery text-center clean table--cart">
                                        <thead>
                                        <tr class="main-heading">
                                            <th scope="col">{{ __('Image') }}</th>
                                            <th scope="col">{{ __('Name') }}</th>
                                            <th scope="col">{{ __('Price') }}</th>
                                            <th scope="col">{{ __('Quantity') }}</th>
                                            <th scope="col">{{ __('Subtotal') }}</th>
                                            <th scope="col">{{ __('Remove') }}</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach(Cart::instance('cart')->content() as $key => $cartItem)
                                            @php
                                                $product = $products->find($cartItem->id);
                                            @endphp

                                            @if (!empty($product))
                                                <tr>
                                                    <td class="image product-thumbnail">
                                                        <input type="hidden" name="items[{{ $key }}][rowId]" value="{{ $cartItem->rowId }}">
                                                        <a href="{{ $product->original_product->url }}">
                                                            <img src="{{ $cartItem->options['image'] }}" alt="{{ $product->name }}" />
                                                        </a>
                                                    </td>
                                                    <td class="product-des product-name">
                                                        <p class="product-name"><a href="{{ $product->original_product->url }}">{{ $product->name }}  @if ($product->isOutOfStock()) <span class="stock-status-label">({!! $product->stock_status_html !!})</span> @endif</a></p>
                                                        <p class="mb-0"><small>{{ $cartItem->options['attributes'] ?? '' }}</small></p>

                                                        @if (!empty($cartItem->options['options']))
                                                            {!! render_product_options_info($cartItem->options['options'], $product, true) !!}
                                                        @endif

                                                        @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                                            @foreach($cartItem->options['extras'] as $option)
                                                                @if (!empty($option['key']) && !empty($option['value']))
                                                                    <p class="mb-0"><small>{{ $option['key'] }}: <strong> {{ $option['value'] }}</strong></small></p>
                                                                @endif
                                                            @endforeach
                                                        @endif
                                                    </td>
                                                    <td class="price" data-title="{{ __('Price') }}">
                                                        <span>{{ format_price($cartItem->price) }}</span>
                                                        @if ($product->front_sale_price != $product->price)
                                                            <small><del>{{ format_price($product->price) }}</del></small>
                                                        @endif
                                                    </td>

                                                    <td class="text-center" data-title="{{ __('Quantity') }}">
                                                        <div class="detail-qty border radius  m-auto">
                                                            <a href="#" class="qty-down"><i class="fa fa-caret-down" aria-hidden="true"></i></a>
                                                            <input type="number" min="1" value="{{ $cartItem->qty }}" name="items[{{ $key }}][values][qty]" class="qty-val qty-input" />
                                                            <a href="#" class="qty-up"><i class="fa fa-caret-up" aria-hidden="true"></i></a>
                                                        </div>
                                                    </td>
                                                    <td class="text-right" data-title="{{ __('Subtotal') }}">
                                                        <span>{{ format_price($cartItem->price * $cartItem->qty) }}</span>
                                                    </td>
                                                    <td class="action" data-title="{{ __('Remove') }}">
                                                        <a href="#" class="text-muted remove-cart-button " data-url="{{ route('public.cart.remove', $cartItem->rowId) }}"><i class="fa fa-trash-alt  " style="color: red; font-size: 12pt;"></i></a>
                                                    </td>
                                                </tr>
                                            @endif
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="cart-action row">
                                    <div class="col-12">
                                        {{--                                    <div class="heading_s1 mb-3">--}}
                                        {{--                                        <h4>{{ __('Apply Coupon') }}</h4>--}}
                                        {{--                                    </div>--}}
                                        <div class="total-amount">
                                            <div class="left">
                                                <div class="coupon form-coupon-wrapper">
                                                    <div class="form-row row justify-content-center">
                                                        <div class="form-group col-lg-6">
                                                            <input class="font-medium coupon-code" type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="{{ __('Enter coupon code') }}">
                                                        </div>
                                                        <div class="form-group col-lg-6">
                                                            <button class="btn btn-rounded btn-sm btn-apply-coupon-code" type="button" data-url="{{ route('public.coupon.apply') }}"><i class="far fa-bookmark mr-5"></i>{{ __('Apply') }}</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                        <div class="col-4">
                            <div class="row mb-50">

                                <div class="col-lg-12 col-md-12">
                                    <div class="border p-md-4 p-30 border-radius-10 cart-totals">
                                        {{--                                    <div class="heading_s1 mb-3">--}}
                                        {{--                                        <h4>{{ __('Cart Total') }}</h4>--}}
                                        {{--                                    </div>--}}
                                        <div class="table-responsive">
                                            <table class="table">
                                                <tbody>
                                                @if (EcommerceHelper::isTaxEnabled())
                                                    <tr>
                                                        <td class="cart_total_label">{{ __('Tax') }}</td>
                                                        <td class="cart_total_amount"><span class="font-lg fw-900 text-brand">{{ format_price(Cart::instance('cart')->rawTax()) }}</span></td>
                                                    </tr>
                                                @endif
                                                @if ($couponDiscountAmount > 0 && session('applied_coupon_code'))
                                                    <tr>
                                                        <td class="cart_total_label">{{ __('Coupon code: :code', ['code' => session('applied_coupon_code')]) }} (<small><a class="btn-remove-coupon-code text-danger" data-url="{{ route('public.coupon.remove') }}" href="javascript:void(0)" data-processing-text="{{ __('Removing...') }}">{{ __('Remove') }}</a></small>)<span></td>
                                                        <td class="cart_total_amount"><span class="font-lg fw-900 text-brand">{{ format_price($couponDiscountAmount) }}</span></td>
                                                    </tr>
                                                @endif
                                                @if ($promotionDiscountAmount)
                                                    <tr>
                                                        <td class="cart_total_label">{{ __('Discount promotion') }}</td>
                                                        <td class="cart_total_amount"><span class="font-lg fw-900 text-brand">{{ format_price($promotionDiscountAmount) }}</span></td>
                                                    </tr>
                                                @endif
                                                @if(auth('customer'))
                                                    @php
                                                        session()->forget('shippingAmount');
                                                            $address=Botble\Ecommerce\Models\Address::where('customer_id', auth('customer')->user()->id)->first();
                                                            $customerType=auth('customer')->user()->type;
                                                            $region= $address->state;



                                                            $weight=0.00;
                                                            $IVAPERCENTAGE=1.22;
$orderAmount=Cart::instance('cart')->rawTotal();
//@dd($region, $customerType,$orderAmount);
 if ($customerType == ('StudioMedico'||'Dentista')) {
            return 'To be determined';
        }
        if ($region == ('campania'||'lazio') && $customerType == ('Farmacia'||'Parafarmacia'||'AltroPharma') && $orderAmount < 300) {
              dd('10,00 euros');

        }
        if ($region == ('campania'||'lazio') && $customerType == ('Farmacia'||'Parafarmacia'||'AltroPharma') && $orderAmount >= 300) {
            return '5,00 euros';
              dd('5,00 euros');
        }
        if ($customerType == ('Farmacia'||'Parafarmacia'||'AltroPharma') ) {

              dd('10,00 euros');
        }
if ($region == ('campania'||'lazio') && $customerType == 'Farmacia' && $orderAmount < 300) {
//            return '10,00 euros';
             dd('ok');
        }else{
     dd('no');
                 }

                                                            foreach (Cart::instance('cart')->content() as $key => $cartItem) {
                                                                $product = $products->find($cartItem->id);
                                                                $weight=$weight+($product->weight * $cartItem->qty);
                                                            }
                                                            $shippingAmount=0;//shippingAmount needs to be calculated by products weights and prices



                                                            if(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount <= 350.00){
                                                                if($weight>50000.00){
                                                                    $extraWeight=intval($weight/50000.00);
                                                                    $shippingAmount=(29.00+5*($extraWeight )) *$IVAPERCENTAGE;
                                                                }else{
                                                                    $shippingAmount=29.00*$IVAPERCENTAGE;
                                                                }
                                                            }elseif(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount > 350.00 && Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount <= 600.00 ) {
                                                                if($weight>50000.00){
                                                                    $extraWeight=intval($weight/50000.00);
                                                                    $shippingAmount=(12.90+5*($extraWeight )) *$IVAPERCENTAGE;
                                                                }else{
                                                                    $shippingAmount=12.90*$IVAPERCENTAGE;
                                                                }
                                                            }else{
                                                                if($weight>50000.00){
                                                                    $extraWeight=intval($weight/50000.00);
                                                                    $shippingAmount=(7.90+5*($extraWeight )) *$IVAPERCENTAGE;
                                                                }else{
                                                                    $shippingAmount=7.90*$IVAPERCENTAGE;
                                                                }
                                                            }

                                                    @endphp
                                                @endif

                                                <input type="hidden" name="shippingAmount" value="{{ $shippingAmount }}">

                                                <tr>
                                                    <td class="cart_total_label">{{ __('Total') }} <small>({{ __('Shipping fees not included') }})</small></td>
                                                    <td class="cart_total_amount"><strong><span class="font-xl fw-900 text-brand">{{ ($promotionDiscountAmount + $couponDiscountAmount) > Cart::instance('cart')->rawTotal() ? format_price(0) : format_price(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount) }}</span></span></strong></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>


                    </div>
                    <div class="row">
                        @if (Cart::instance('cart')->count() > 0)
                            <div class="col-8 mt-30">
                                <div class="row">
                                    <div class="col-6" style="align-self: flex-end">
                                        <a class="btn btn-rounded" href="{{ route('public.products') }}"><i class="far fa-cart-plus mr-5"></i>{{ __('Continue Shopping') }}</a>
                                    </div>
                                    <div class="col-6">
                                        <button type="submit" name="checkout" class="btn btn-rounded"> <i class="fa fa-share-square mr-10"></i> {{ __('Proceed To Checkout') }}</button>
                                    </div>
                                </div>



                            </div>
                            {{--                        <div class="divider center_icon mt-50 mb-50"><i class="fa fa-gem"></i></div>--}}


                        @endif
                    </div>



                @else
                    <p class="text-center">{{ __('Your cart is empty!') }}</p>
                @endif
            </div>
        </div>
    </div>
</section>

@if (count($crossSellProducts) > 0)
    <div class="row mt-60">
        <div class="col-12">
            <h3 class="section-title style-1 mb-30">{{ __('You may also like') }}</h3>
        </div>
        @foreach($crossSellProducts as $crossProduct)
            <div class="col-lg-3 col-md-4 col-12 col-sm-6">
                @include(Theme::getThemeNamespace() . '::views.ecommerce.includes.product-item', ['product' => $crossProduct])
            </div>
        @endforeach
    </div>
@endif
