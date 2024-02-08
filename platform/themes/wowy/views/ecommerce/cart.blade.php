@php
    use App\Http\Controllers\SuggestionController;
    use Botble\Ecommerce\Models\OffersDetail;
    use Botble\Ecommerce\Models\Offers;
    use Botble\Ecommerce\Models\Product;
    use Botble\Ecommerce\Models\ProductVariation;
    // use Botble\Ecommerce\Models\CarouselProducts;
    // use Botble\Ecommerce\Models\SPC;
    // if (request()->user('customer')) {
    //     $userid = request()->user('customer')->id;
    //     if (!CarouselProducts::where('customer_id', $userid)->exists()) {
    //         $discountedProducts = SuggestionController::getProduct($userid);
    //     } else {
    //         $productIds = CarouselProducts::where('customer_id', $userid)->pluck('product_id');
    //         $discountedProducts = Product::whereIn('id', $productIds)->get();
    //     }
    // }
@endphp
<section class="mt-60 mb-20">
    <div class="container">
        <div class="row">
            <div class="col-12 section--shopping-cart">
                <form class="form--shopping-cart" method="post" action="{{ route('public.cart.update') }}">
                    @csrf
                    @if (count($products) > 0)
                        <div class="row">
                            <div class="col-8">
                                <div class="table-responsive">
                                    <table class="table shopping-summery text-center clean table--cart">
                                        <thead>
                                            <tr class="main-heading">
                                                <th scope="col"></th>
                                                <th scope="col">{{ __('Name') }}</th>
                                                <th scope="col">{{ __('Prezzo') }}</th>
                                                <th scope="col">{{ __('Quantità') }}</th>
                                                <th scope="col">{{ __('Totale parziale') }}</th>
                                                <th scope="col">{{ __('Remove') }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @php
                                                $cartTotal = Cart::instance('cart')->rawSubTotal();
                                                $cartIva = Cart::instance('cart')->rawTax();
                                                $userid = request()->user('customer')->id;
                                                if ($userid == 11 || $userid == 13) {
                                                    $userid = 2621;
                                                }
                                            @endphp
                                            @foreach (Cart::instance('cart')->content() as $key => $cartItem)
                                                @php
                                                    $flag = false; // Reset flag for each item
                                                    $product = Product::find($cartItem->id);
                                                    if ($product && $product->is_variation) {
                                                        $AllVariations = Product::where('name', $cartItem->name)->get();
                                                        foreach ($AllVariations as $variation) {
                                                            if ($variation->is_variation) {
                                                                $flag = true;
                                                                break; // Found a variation, no need to continue
                                                            }
                                                        }
                                                    }
                                                    if ($flag) {
                                                        $productVariation = ProductVariation::where('product_id', $cartItem->id)->first();
                                                        $product_id = $productVariation ? $productVariation->configurable_product_id : $cartItem->id;
                                                    } else {
                                                        $product_id = $cartItem->id;
                                                    }
                                                    $product = Product::find($product_id)->first();
                                                    $pricelist = DB::connection('mysql')->select("select * from ec_pricelist where product_id=$product_id and customer_id=$userid");
                                                    if ($pricelist) {
                                                        $offerDetail = OffersDetail::where('product_id', $product_id)->where('customer_id', $userid)->first();
                                                        if ($offerDetail) {
                                                            $offer = Offers::find($offerDetail->offer_id);
                                                            if ($offer) {
                                                                $offerType = $offer->offer_type;
                                                                if ($offerType == 4 && $cartItem->qty >= 3) {
                                                                    $cartTotal = $cartTotal - $cartItem->price * floor($cartItem->qty / 3);
                                                                    $tax = str_replace('€', '', $cartItem->tax());
                                                                    $tax = str_replace(',', '.', $tax);
                                                                    $cartIva = $cartIva - floatval(floatval($tax) * floor($cartItem->qty / 3));
                                                                }
                                                                if ($offerType == 6 && $cartItem->qty >= $offerDetail->quantity) {
                                                                    $tax = str_replace('€', '', $cartItem->tax());
                                                                    $tax = str_replace(',', '.', $tax);
                                                                    $cartIva = $cartIva - floatval($tax) * $cartItem->qty + (($product->tax->percentage * $offerDetail->product_price) / 100) * $cartItem->qty;
                                                                    $cartTotal = $cartTotal - $cartItem->price * $cartItem->qty + $offerDetail->product_price * $cartItem->qty;
                                                                    $cartItem->price = $offerDetail->product_price;
                                                                }
                                                            }
                                                        } else {
                                                            $cartItem->price = $pricelist[0]->final_price;
                                                        }
                                                    }
                                                @endphp

                                                @if (!empty($product))
                                                    <tr>
                                                        <td class="image product-thumbnail">
                                                            <input type="hidden"
                                                                name="items[{{ $key }}][rowId]"
                                                                value="{{ $cartItem->rowId }}">
                                                            <a href="{{ $product->original_product->url }}">
                                                                @php
                                                                    $defaultImgUrl = RvMedia::getImageUrl(RvMedia::getDefaultImage());
                                                                    $productImgUrl = RvMedia::getImageUrl($cartItem->options['image']);
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

                                                                <img class="default-img" src="{{ $Image }}"
                                                                    alt="{{ $product->name }}">

                                                            </a>
                                                        </td>
                                                        <td class="product-des product-name">
                                                            <p class="product-name"><a
                                                                    href="{{ $product->original_product->url }}">{{ $cartItem->name }}
                                                                    @if ($pricelist)
                                                                        @if ($offerDetail)
                                                                            @if ($offerType == 1 || $offerType == 2 || $offerType == 3)
                                                                                <span class="badge badge-secondary"
                                                                                    style="background: #E52728;font-size:smaller">{{ get_sale_percentage($product->price, $offerDetail->product_price) }}</span>
                                                                            @elseif ($offerType == 4)
                                                                                <span class="badge badge-secondary"
                                                                                    style="background: #E52728;font-size:smaller">3x2</span>
                                                                            @elseif ($offerType == 5)
                                                                                <span class="badge badge-secondary"
                                                                                    style="background: #E52728;font-size:smaller"><i
                                                                                        class="fa fa-link"></i></span>
                                                                            @elseif ($offerType == 6 && $cartItem->qty >= $offerDetail->quantity)
                                                                                {{-- It's okay --}}
                                                                                @php
                                                                                    if ($pricelist) {
                                                                                        $priceOfProduct = $pricelist[0]->final_price;
                                                                                    } else {
                                                                                        $priceOfProduct = $product->price;
                                                                                    }
                                                                                @endphp
                                                                                <span class="badge badge-secondary"
                                                                                    style="background: #E52728;font-size:smaller">{{ get_sale_percentage($offerDetail->product_price, $priceOfProduct) }}</span>
                                                                            @endif
                                                                        @endif
                                                                    @endif

                                                                    @if ($product->isOutOfStock())
                                                                        <span
                                                                            class="stock-status-label">({!! $product->stock_status_html !!})</span>
                                                                    @endif
                                                                </a></p>
                                                            <p class="mb-0">
                                                                <small>{{ $cartItem->options['attributes'] ?? '' }}</small>
                                                            </p>

                                                            @if (!empty($cartItem->options['options']))
                                                                {!! render_product_options_info($cartItem->options['options'], $product, true) !!}
                                                            @endif

                                                            @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                                                @foreach ($cartItem->options['extras'] as $option)
                                                                    @if (!empty($option['key']) && !empty($option['value']))
                                                                        <p class="mb-0">
                                                                            <small>{{ $option['key'] }}:
                                                                                <strong>
                                                                                    {{ $option['value'] }}
                                                                                </strong>
                                                                            </small>
                                                                        </p>
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
                                                                <a href="#" class="qty-down"><i
                                                                        class="fa fa-caret-down"
                                                                        aria-hidden="true"></i></a>
                                                                <input type="number" min="1"
                                                                    value="{{ $cartItem->qty }}"
                                                                    name="items[{{ $key }}][values][qty]"
                                                                    class="qty-val qty-input" />
                                                                <a href="#" class="qty-up"><i
                                                                        class="fa fa-caret-up"
                                                                        aria-hidden="true"></i></a>
                                                            </div>
                                                        </td>
                                                        <td class="text-right" data-title="{{ __('Subtotal') }}">
                                                            @if ($pricelist)
                                                                @if ($offerDetail)
                                                                    @if ($offerType == 4 && $cartItem->qty >= 3)
                                                                        <span>{{ format_price($cartItem->price * ($cartItem->qty - floor($cartItem->qty / 3))) }}</span>
                                                                        <span>
                                                                            <del
                                                                                style="display:block;font-size: xx-small">{{ format_price($cartItem->price * $cartItem->qty) }}</del>
                                                                        </span>
                                                                    @elseif ($offerType == 6 && $cartItem->qty >= $offerDetail->quantity)
                                                                        <span>{{ format_price($cartItem->price * $cartItem->qty) }}</span>
                                                                        <span>
                                                                            <del
                                                                                style="display:block;font-size: xx-small">{{ format_price($pricelist->final_price * $cartItem->qty) }}</del>
                                                                        </span>
                                                                    @else
                                                                        <span>{{ format_price($cartItem->price * $cartItem->qty) }}</span>
                                                                    @endif
                                                                @else
                                                                    <span>{{ format_price($cartItem->price * $cartItem->qty) }}</span>
                                                                @endif
                                                            @endif
                                                        </td>
                                                        <td class="action" data-title="{{ __('Remove') }}">
                                                            <a href="#" class="text-muted remove-cart-button "
                                                                data-url="{{ route('public.cart.remove', $cartItem->rowId) }}"><i
                                                                    class="fa fa-trash-alt  "
                                                                    style="color: red; font-size: 12pt;"></i></a>
                                                        </td>
                                                    </tr>
                                                    @if ($pricelist)
                                                        @if ($offerDetail)
                                                            @if ($offerType == 5)
                                                                @php
                                                                    $collegati_id = $offerDetail->gift_product_id;
                                                                    $collegati = Product::find($collegati_id);
                                                                @endphp
                                                                <tr
                                                                    style="position: relative; background-color:#d7f7d8">


                                                                    <td class="image product-thumbnail">
                                                                        {{--  <input type="hidden" name="items[{{ $key }}][rowId]" value="{{ $cartItem->rowId }}">  --}}
                                                                        <span>
                                                                            {{-- <img src="{{ $cartItem->options['image'] }}" alt="{{ $product->name }}" /> --}}
                                                                            <h6>{{ $collegati->sku }}</h6>
                                                                        </span>
                                                                    </td>
                                                                    <td class="product-des product-name">
                                                                        <p class="product-name">
                                                                            <span>{{ $collegati->name }} &nbsp;
                                                                                @if ($product->isOutOfStock())
                                                                                    <span
                                                                                        class="stock-status-label">({!! $product->stock_status_html !!})</span>
                                                                                @endif
                                                                            </span>
                                                                        </p>
                                                                        <p class="mb-0">
                                                                            <small>{{ $cartItem->options['attributes'] ?? '' }}</small>
                                                                        </p>

                                                                        @if (!empty($cartItem->options['options']))
                                                                            {!! render_product_options_info($cartItem->options['options'], $product, true) !!}
                                                                        @endif

                                                                        @if (!empty($cartItem->options['extras']) && is_array($cartItem->options['extras']))
                                                                            @foreach ($cartItem->options['extras'] as $option)
                                                                                @if (!empty($option['key']) && !empty($option['value']))
                                                                                    <p class="mb-0">
                                                                                        <small>{{ $option['key'] }}:
                                                                                            <strong>
                                                                                                {{ $option['value'] }}</strong></small>
                                                                                    </p>
                                                                                @endif
                                                                            @endforeach
                                                                        @endif
                                                                    </td>
                                                                    <td class="price">

                                                                        <span>{{ format_price(0) }}</span>
                                                                        <small
                                                                            style="display:block"><del>{{ format_price($collegati->price) }}</del></small>
                                                                    </td>

                                                                    <td class="text-center">
                                                                        <div class="detail-qty border radius  m-auto">
                                                                            <input type="number" disabled
                                                                                min="1"
                                                                                value="{{ 1 }}"
                                                                                name="collegati[]"
                                                                                class="qty-val qty-input" />
                                                                        </div>
                                                                    </td>
                                                                    <td colspan="2" class="text-right"
                                                                        data-title="{{ __('Subtotal') }}">
                                                                    </td>

                                                                </tr>
                                                            @endif
                                                            @if ($pricelist)
                                                                @if ($offerDetail)
                                                                    @if ($offerType == 6)
                                                                        <tr class="alert alert-danger">
                                                                            <td colspan="6">se la quantità di questo
                                                                                prodotto
                                                                                sarà superiore a
                                                                                {{ $offerDetail->quantity }}
                                                                                avrai uno sconto del
                                                                                @php
                                                                                    if ($pricelist) {
                                                                                        $priceOfProduct = $pricelist[0]->final_price;
                                                                                    } else {
                                                                                        $priceOfProduct = $product->price;
                                                                                    }
                                                                                @endphp
                                                                                {{ get_sale_percentage($offerDetail->product_price, $priceOfProduct) }}
                                                                                solo su questo prodotto</td>
                                                                        </tr>
                                                                    @endif
                                                                @endif
                                                            @endif
                                                        @endif
                                                    @endif
                                                @endif
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                                <div class="cart-action row">
                                    <div class="col-12">
                                        {{--                                    <div class="heading_s1 mb-3"> --}}
                                        {{--                                        <h4>{{ __('Apply Coupon') }}</h4> --}}
                                        {{--                                    </div> --}}
                                        <div class="total-amount">
                                            <div class="left">
                                                <div class="coupon form-coupon-wrapper">
                                                    <div class="form-row row justify-content-center">
                                                        <div class="form-group col-lg-9">
                                                            <input class="font-medium coupon-code" type="text"
                                                                name="coupon_code" value="{{ old('coupon_code') }}"
                                                                placeholder="{{ __('Enter coupon code') }}">
                                                        </div>
                                                        <div class="form-group col-lg-3">
                                                            <button
                                                                class="col-12 btn btn-rounded btn-sm btn-apply-coupon-code"
                                                                type="button"
                                                                data-url="{{ route('public.coupon.apply') }}"><i
                                                                    class="far fa-bookmark mr-5"></i>Applica
                                                                Coupon</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            @if (Cart::instance('cart')->count() > 0)
                                                <div class="col-12 mt-30">
                                                    <div class="row">
                                                        <div class="col-6" style="align-self: flex-end">
                                                            <a href="/empty-cart"
                                                                class='mt-5 btn btn-danger btn-outline col-12 empty-card'
                                                                style='border-radius:50px'><i
                                                                    class='fa fa-trash-alt mr-2'></i> Svuota il
                                                                Carrello</a>
                                                            {{--                                        <a class="btn btn-rounded" href="{{ route('public.products') }}"><i class="far fa-cart-plus mr-5"></i>{{ __('Continue Shopping') }}</a> --}}
                                                        </div>
                                                        <div class="col-6">
                                                            <button type="submit" name="checkout"
                                                                class="col-12 btn btn-rounded"> <i
                                                                    class="fa fa-share-square mr-10"></i>
                                                                {{ __('Proceed To Checkout') }}</button>
                                                        </div>
                                                    </div>



                                                </div>
                                                {{--                        <div class="divider center_icon mt-50 mb-50"><i class="fa fa-gem"></i></div> --}}
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="row mb-50">

                                    <div class="col-lg-12 col-md-12">
                                        <div class="border p-md-4 p-30 border-radius-10 cart-totals">
                                            <div class="table-responsive">
                                                <table class="table">
                                                    <tbody>

                                                        @if ($couponDiscountAmount > 0 && session('applied_coupon_code'))
                                                            <tr>
                                                                <td class="cart_total_label">
                                                                    {{ __('Coupon code: :code', ['code' => session('applied_coupon_code')]) }}
                                                                    (<small><a
                                                                            class="btn-remove-coupon-code text-danger"
                                                                            data-url="{{ route('public.coupon.remove') }}"
                                                                            href="javascript:void(0)"
                                                                            data-processing-text="{{ __('Removing...') }}">{{ __('Remove') }}</a></small>)<span>
                                                                </td>
                                                                <td class="cart_total_amount"><span
                                                                        class="font-lg fw-900 text-brand">{{ format_price($couponDiscountAmount) }}</span>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if ($promotionDiscountAmount)
                                                            <tr>
                                                                <td class="cart_total_label">
                                                                    {{ __('Discount promotion') }}</td>
                                                                <td class="cart_total_amount"><span
                                                                        class="font-lg fw-900 text-brand">{{ format_price($promotionDiscountAmount) }}</span>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        @if (auth('customer'))
                                                            @php
                                                                session()->forget('shippingAmount');
                                                                $address = Botble\Ecommerce\Models\Address::where('customer_id', auth('customer')->user()->id)->first();
                                                                $customerType = auth('customer')->user()->type;
                                                                $region = $address->state;

                                                                $weight = 0.0;
                                                                $IVAPERCENTAGE = 1.22;
                                                                $orderAmount = Cart::instance('cart')->rawTotal();

                                                                if ($region == ('campania' || 'lazio') && $customerType == ('Farmacia' || 'Parafarmacia' || 'AltroPharma') && $orderAmount < 300) {
                                                                    $shippingAmount = 10;
                                                                }
                                                                if ($region == ('campania' || 'lazio') && $customerType == ('Farmacia' || 'Parafarmacia' || 'AltroPharma') && $orderAmount >= 300) {
                                                                    $shippingAmount = 5;
                                                                }
                                                                if ($customerType == ('Farmacia' || 'Parafarmacia' || 'AltroPharma')) {
                                                                    $shippingAmount = 10;
                                                                }
                                                                $subtotal = Cart::instance('cart')->rawSubTotal();
                                                                //@dd(Cart::instance('cart')->subTotal());

                                                                //                                                            foreach (Cart::instance('cart')->content() as $key => $cartItem) {
                                                                //                                                                $product = $products->find($cartItem->id);
                                                                //                                                                $weight=$weight+($product->weight * $cartItem->qty);
                                                                //                                                            }
                                                                //                                                            $shippingAmount=0;//shippingAmount needs to be calculated by products weights and prices
                                                                //
                                                                //
                                                                //
                                                                //                                                            if(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount <= 350.00){
                                                                //                                                                if($weight>50000.00){
                                                                //                                                                    $extraWeight=intval($weight/50000.00);
                                                                //                                                                    $shippingAmount=(29.00+5*($extraWeight )) *$IVAPERCENTAGE;
                                                                //                                                                }else{
                                                                //                                                                    $shippingAmount=29.00*$IVAPERCENTAGE;
                                                                //                                                                }
                                                                //                                                            }elseif(Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount > 350.00 && Cart::instance('cart')->rawTotal() - $promotionDiscountAmount - $couponDiscountAmount <= 600.00 ) {
                                                                //                                                                if($weight>50000.00){
                                                                //                                                                    $extraWeight=intval($weight/50000.00);
                                                                //                                                                    $shippingAmount=(12.90+5*($extraWeight )) *$IVAPERCENTAGE;
                                                                //                                                                }else{
                                                                //                                                                    $shippingAmount=12.90*$IVAPERCENTAGE;
                                                                //                                                                }
                                                                //                                                            }else{
                                                                //                                                                if($weight>50000.00){
                                                                //                                                                    $extraWeight=intval($weight/50000.00);
                                                                //                                                                    $shippingAmount=(7.90+5*($extraWeight )) *$IVAPERCENTAGE;
                                                                //                                                                }else{
                                                                //                                                                    $shippingAmount=7.90*$IVAPERCENTAGE;
                                                                //                                                                }
                                                                //                                                            }
                                                            @endphp
                                                        @endif

                                                        <input type="hidden" name="shippingAmount"
                                                            value="{{ $shippingAmount }}">

                                                        <tr>

                                                            <td class="cart_total_label">
                                                                {{ __('Subtotale IVA esclusa') }}
                                                            </td>
                                                            <td class="cart_total_amount"><strong><span
                                                                        class="font-xl fw-900 text-brand">{{ format_price($subtotal) }}</span></strong>
                                                            </td>
                                                        </tr>
                                                        @if (EcommerceHelper::isTaxEnabled())
                                                            <tr>
                                                                <td class="cart_total_label">{{ __('Tax') }}</td>
                                                                <td class="cart_total_amount"><span
                                                                        class="font-lg fw-900 text-brand">{{ format_price(Cart::instance('cart')->rawTax()) }}</span>
                                                                </td>
                                                            </tr>
                                                        @endif
                                                        <tr>

                                                            <td class="cart_total_label">
                                                                {{ __('Contributo spese di spedizione e imballagio') }}
                                                            </td>
                                                            <td class="cart_total_amount"><strong><span
                                                                        class="font-xl fw-900 text-brand">{{ format_price($shippingAmount) }}</span></strong>
                                                            </td>
                                                        </tr>
                                                        <tr>

                                                            <td class="cart_total_label">
                                                                {{ __('Totale IVA inclusa') }}
                                                            </td>
                                                            <td class="cart_total_amount"><strong><span
                                                                        class="font-xl fw-900 text-brand">{{ format_price($cartTotal) }}</span></strong>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <p style="font-size: 9.5pt !important;">Il contributo per le spese di
                                                spedizione ed imballaggio è calcolato sull'importo dell'ordine al netto
                                                di
                                                eventuali omaggi a cui si ha diritto. <br>
                                                Questi ultimi saranno stornati dal totale, in fase di gestione
                                                dell'ordine.
                                            </p>
                                            <p style='font-size: 11pt; color: black'><label for="comunicarci"><b>Hai
                                                        qualcosa da comunicarci?</b></label></p>
                                            <textarea id="comunicarci" name="note" rows="5" cols="37" style='min-height: unset!important'
                                                placeholder="Scrivi qui eventuali note per l'ordine">
                                                @if (Session::get('note') != '')
{{ Session::has('note') ? Session::get('note') : '' }}
@endif
                                            </textarea>

                                        </div>
                                    </div>
                                </div>
                            </div>


                        </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-12">
                <div class="error-container"
                    style="background-color: #fff;
                            padding: 20px;text-align: center;
                            border-radius: 5px;">
                    <div class="error-icon"
                        style="font-size: 90px;
                                color: #777;
                                margin-bottom: 20px;">
                        <i class="far fa-shopping-cart"></i>
                    </div>
                    <p style="font-size: 12pt;
                                font-weight: 600;">Il suo
                        carello è vuoto!</p>
                </div>
            </div>
        </div>
        @endif

    </div>
</section>

{{-- @if (request()->user('customer'))

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

@endif --}}
