@extends(BaseHelper::getAdminMasterLayoutTemplate())
@section('content')

    {{-- {!! Form::open() !!} --}}
        <div class="container" id="main-discount">
            <div class="max-width-1200 row">
                <div class="col-md-12">
                    <h4>Crea una promozione di sconto</h4>
                    <input type="text" class="form-control" placeholder="Inserisci il nome della promozione" name="title">
                    <hr>
                </div>
                <div class="col-md-6">
                    <h5>Sconto Percentuale % </h5>
                    <input id='sconto' type="text" class="form-control" name="value" placeholder="10">
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
                                        <li class="row">

                                        </li>
                                        <div class="navigation">

                                        </div>
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
                    <div class="accordion my-2" id="accordionExample ">
                        <div class="accordion-item">
                            <div class="accordion-header " id="headingOne">
                                <button class="accordion-button py-2 px-4" id="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse4" aria-expanded="true" aria-controls="collapseOne"  style="background-color: white;color:black;">
                                    Regione
                                </button>
                            </div>
                            <div id="collapse4" class="accordion-collapse collapse " aria-labelledby="headingOne" data-bs-parent="#accordionExample" >
                                <div class="accordion-body" >
                                    @php
                                        $regione=DB::connection('mysql')->select('select * from cities');
                                    @endphp
                                    @foreach($regione as $reg)
                                                <input class="form-check-input region-check"
                                                       name="regione[]"
                                                       type="checkbox"
                                                       id="brand-filter-{{ $reg->id}}"
                                                       value="{{ $reg->id}}"
                                                       @if (in_array($reg->id, request()->input('regione', []))) checked @endif>
                                                <label class="form-check-label" for="brand-filter-{{ $reg->id }}"><span class="d-inline-block">{{$reg->name}}</span>  </label>
                                        <br>
                                    @endforeach

                                </div>
                            </div>
                        </div>

                    </div>
                    <hr>
                </div>
                <form action="https://dev.marigo.collaudo.biz/admin/ecommerce/customImport/sconto" method="post" class="sconto-form">
                    @csrf
                    <input type="submit" class="btn btn-primary mb-5 col-6" name="submit" value="Creare Sconto">
                </form>


                <div class="alert alert-success update-alert hidden" role="alert">

                </div>
                <br>
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Initial price</th>
                        <th scope="col">Final price</th>
                      </tr>
                    </thead>
                    <tbody class='product-row'>

                    </tbody>
                  </table>
            </div>
        </div>
    {{-- {!! Form::close() !!} --}}
@stop

@push('header')
<style>
    .panel{
        position: absolute;
        background-color: white;
        padding: 15px;
        align-content: space-between;
        display: inline-block;
        z-index: 99999;
        min-width: 300px;
        transform: translateX(10px);
        top: 35px;
        border: none;
        box-shadow: rgba(0, 0, 0, 0.16) 0px 1px 4px !important
    }
    .prev-page{
        color: mediumblue;
        text-transform: uppercase;
        cursor: pointer;
    }
    .next-page{
    margin-left: 125px;
    color: mediumblue;
    text-transform: uppercase;
    cursor: pointer;
    }
    #div-select-product,
    #div-select-customer{

        position: relative;

    }
    .list-search-data .clearfix .row{
        padding: 15px;
    cursor: pointer;
    }
    .list-search-data .clearfix .row:hover{
        color: mediumblue;
    background-color: #f5f5f5;
    }

    .panel-default .panel-body{
        padding-bottom: none !important;
    }

</style>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.4.0/axios.min.js" integrity="sha512-uMtXmF28A2Ab/JJO2t/vYhlaa/3ahUOgj1Zf27M5rOo8/+fcTUVH0/E0ll68njmjrLqOBjXM3V9NiPFL5ywWPQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script>
        'use strict';

        window.trans = window.trans || {};

        window.trans.discount = JSON.parse('{!! addslashes(json_encode(trans('plugins/ecommerce::discount'))) !!}');

        $(document).ready(function () {


            $('body').click(function(evt){
                let container = $('#div-select-product');
                if(evt.target.id == "product-search-realtime")
                    container.find('.panel').removeClass('hidden');
                //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
                if($(evt.target).closest('#product-search-realtime').length)
                    return;

                container.find('.panel').addClass('hidden');
                //Do processing of click event here for every element except with id menu_content

            });


            $('body').click(function(evt){
                let container = $('#div-select-customer');
                if(evt.target.id == "customer-search-realtime")
                    container.find('.panel').removeClass('hidden');
                //For descendants of menu_content being clicked, remove this check if you do not want to put constraint on descendants.
                if($(evt.target).closest('#customer-search-realtime').length)
                    return;

                container.find('.panel').addClass('hidden');
                //Do processing of click event here for every element except with id menu_content

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
                            $('#div-select-product .panel .panel-body .list-search-data .clearfix').append("<li class='row product-select-btn' data-sku='"+element.sku+"' data-value='"+element.price+"'>"+element.name+"</li>");

                        });
                        $('#div-select-product .panel .panel-body .list-search-data .navigation').append("<span class='prev-page' data-href='"+response.data.data.prev_page_url+"'>prev page</span>")
                        $('#div-select-product .panel .panel-body .list-search-data .navigation').append("<span class='next-page' data-href='"+response.data.data.next_page_url+"'>next page</span>")
                        console.log(response.data);
                    })

                    .catch((err) => console.log(err));
                    });


                $('#customer-search-realtime').keyup(function(){
                const keyword=$(this).val()
                if($('#customer-search-current-page').val()!=''){
                    var page=$('#customer-search-current-page').val()
                }
                else{var page=1}

                axios
                    .get("https://dev.marigo.collaudo.biz/admin/customers/get-list-customers-for-search", { params:{
                    keyword: $(this).val(),
                    include_variation: 0,
                    page: page
                    }})
                    .then((response) => {
                        $("#div-select-customer .panel").removeClass('d-none');
                        const products=response.data.data.data;
                        $('#div-select-customer .panel .panel-body .list-search-data .clearfix').html('');
                        $('#div-select-customer .panel .panel-body .list-search-data .navigation').html('');

                        products.forEach(element => {
                        $('#div-select-customer .panel .panel-body .list-search-data .clearfix').append("<li class='row customer-select-btn' data-href='"+element.id+"' data-value='"+element.email+"'>"+element.name+"</li>");
                        });
                        $('#div-select-customer .panel .panel-body .list-search-data .navigation').append("<span class='prev-page' data-href='"+response.data.data.prev_page_url+"'>prev page</span>")
                        $('#div-select-customer .panel .panel-body .list-search-data .navigation').append("<span class='next-page' data-href='"+response.data.data.next_page_url+"'>next page</span>")
                        console.log(response.data);
                    })

                    .catch((err) => console.log(err));
                    });



            });
            $(document).on("click", ".product-select-btn", function(){
                var sku=$(this).attr('data-sku');
                $('.sconto-form').append("<input type='hidden' name='products[]' value="+sku+">");
                var price=$(this).attr('data-value');
                var name=$(this).text();
                var sconto=$('#sconto').val();
                $('.product-row').append(`
                <tr>
                    <td>${sku}</td>
                    <td>${name}</td>
                    <td>${price}</td>
                    <td>${(price*(100-sconto))/100}</td>
                </tr>
                `);
            });


            $(document).on("click", ".customer-select-btn", function(){
                var id=$(this).attr('data-href');
                var inputs=$(".sconto-form input[name='users[]']");

                var hasValue = false;
                inputs.each(function() {
                // Check if the value of the current input is equal to 12
                if ($(this).val() === id) {
                    hasValue = true;
                    // Break out of the loop if you want to stop checking after finding the first occurrence
                    return false;
                }
                });

                // Check the result
                if (hasValue) {
                    alert("User already included!");
                } else {
                    $('.sconto-form').append("<input type='hidden' name='users[]' value="+id+">");
                var regioncount = $('input:checkbox:checked').length;


                var inputs = $(".sconto-form input[name='users[]']");
                var uniqueValues = {};
                inputs.each(function() {
                var value = $(this).val();
                if (!uniqueValues[value]) {
                    uniqueValues[value] = true;
                }
                });
                var userscount = Object.keys(uniqueValues).length;


                $('.update-alert').removeClass('hidden');
                $('.update-alert').html(`
                    This price list will affect on ${userscount}users and ${regioncount} regions
                `);
                }








                });

            $(document).on("click", ".region-check", function(){

                var id=$(this).attr('value');
                $('.sconto-form').append("<input type='hidden' name='region[]' value="+id+">");
                var regioncount = $('input:checkbox:checked').length;
                var inputs = $(".sconto-form input[name='users[]']");
                var uniqueValues = {};
                inputs.each(function() {
                var value = $(this).val();
                if (!uniqueValues[value]) {
                    uniqueValues[value] = true;
                }
                });
                var userscount = Object.keys(uniqueValues).length;
                $('.update-alert').removeClass('hidden');
                $('.update-alert').html(`
                    This price list will affect on ${userscount} users and ${regioncount} regions
                `);
            });















    </script>
    @php
        Assets::addScripts(['form-validation']);
    @endphp
    {!! JsValidator::formRequest(\Botble\Ecommerce\Http\Requests\DiscountRequest::class) !!}
@endpush
