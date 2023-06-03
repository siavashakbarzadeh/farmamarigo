@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')
    {!! Form::open() !!}
        <div class="container" id="main-discount">
            <div class="max-width-1200 row">
                <div class="col-md-12">
                    <h4>Crea una promozione di sconto</h4>
                    <input type="text" class="form-control" placeholder="Inserisci il nome della promozione" name="title">
                    <hr>
                    {{-- <h5>Seleziona il tipo di sconto</h5>
                    <select id="select-promotion" name="type" onChange="changeDiscountType()">
                        <option value="coupon">{{ __('discount.coupon_code')}}</option>
                        <option value="promotion">{{ __('discount.promotion')}}</option>
                    </select>
                    <hr> --}}
                </div>
                <div class="col-md-6">
                    <h5>Sconto Percentuale % </h5>
                    <input type="text" class="form-control" name="value" placeholder="10">
                    <hr>
                </div>
                <div class="col-md-6">
                    <h5>Cliente</h5>
                    <div id="div-select-customer">
                        <div class="box-search-advance customer" style="min-width:310px;">
                            <input type="text" class=" form-control next-input textbox-advancesearch customer" id="customer-search-realtime" placeholder="Cerca cliente" aria-invalid="false">
                            <input type="hidden" name='current-page' id="customer-search-current-page">
                        </div>
                        <div class="panel panel-default d-none">
                            <div class="panel-body">
                                <div class="list-search-data">
                                    <ul class="clearfix">
                                        <li class="row"></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="col-md-6">
                    <h5>Prodotto</h5>
                    <div id="div-select-product">
                        <div class="box-search-advance product" style="min-width:310px;">
                            <input type="text" class="form-control next-input textbox-advancesearch product" id="product-search-realtime" placeholder="Cerca prodotto" aria-invalid="false">
                            <input type="hidden" name='current-page' id="product-search-current-page">
                        </div>
                        <div class="panel panel-default d-none">
                            <div class="panel-body">
                                <div class="list-search-data">
                                    <ul class="clearfix">

                                    </ul>
                                    <div class="navigation">

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>
                <div class="col-md-6">
                    <h5>Regione</h5>
                    <div id="div-select-region">
                        <div class="box-search-advance customer" style="min-width:310px;">
                            <input type="text" class="form-control next-input textbox-advancesearch region" id="region-search-realtime" placeholder="Cerca regione" aria-invalid="false">
                            <input type="hidden" name='current-page' id="region-search-current-page">
                        </div>
                        <div class="panel panel-default d-none">
                            <div class="panel-body">
                                <div class="list-search-data">
                                    <ul class="clearfix">
                                        <li class="row"></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                </div>


                <discount-component currency="{{ get_application_currency()->symbol }}" date-format="{{ config('core.base.general.date_format.date') }}"></discount-component>
            </div>
        </div>
    {!! Form::close() !!}
@stop

@push('header')
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js" integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        'use strict';

        window.trans = window.trans || {};

        window.trans.discount = JSON.parse('{!! addslashes(json_encode(trans('plugins/ecommerce::discount'))) !!}');

        $(document).ready(function () {
            $(document).on('click', 'body', function (e) {
                let container = $('.box-search-advance');

                if (!container.is(e.target) && container.has(e.target).length === 0) {
                    container.find('.panel').addClass('hidden');
                }
            });

            $('#product-search-realtime').keyup(function(){
                const keyword=$(this).val()
                if($('#product-search-current-page').val()!=''){
                    var page=$('#product-search-current-page').val()
                }
                else{var page=1}

                axios
                    .get("https://dev.marigo.collaudo.biz/admin/ecommerce/products/get-list-products-for-select", { params:{
                    keyword: $(this).val(),
                    include_variation: 0,
                    page: page
                    }})
                    .then((response) => {
                        $("#div-select-product .panel").removeClass('d-none');
                        const products=response.data.data.data;
                        $('#div-select-product .panel .panel-body .list-search-data .clearfix').html('');
                        $('#div-select-product .panel .panel-body .list-search-data .navigation').html('');

                        products.forEach(element => {
                            $('#div-select-product .panel .panel-body .list-search-data .clearfix').append("<li class='row' data-href='"+element.id+"'>"+element.name+"</li>");

                        });
                        $('#div-select-product .panel .panel-body .list-search-data .navigation').append("<span data-href='"+response.data.data.prev_page_url+"'>prev page</span>")
                        $('#div-select-product .panel .panel-body .list-search-data .navigation').append("<span data-href='"+response.data.data.next_page_url+"'>next page</span>")
                        console.log(response.data);
                    })

                    .catch((err) => console.log(err));
                    });
            });







    </script>
    @php
        Assets::addScripts(['form-validation']);
    @endphp
    {!! JsValidator::formRequest(\Botble\Ecommerce\Http\Requests\DiscountRequest::class) !!}
@endpush
