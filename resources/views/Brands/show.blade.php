@extends('core/base::layouts.base')

@section ('page')
    <div class="page-wrapper">

        @include('core/base::layouts.partials.top-header')
        <div class="clearfix"></div>

        <div class="page-container page-container-gray">
            <div class="page-content" style="min-height: calc(100vh - 49px); height: 100%;">
                @yield('content')
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="container-fluid mt-5">
            <div class="row">
                @foreach ($cards as $card)
                    <div class="col-md-3 mb-4">
                        <div class="card">
                            <img src="{{ asset('images/' . $card['image']) }}" class="card-img-top" alt="{{ $card['title'] }}">
                            <div class="card-body">
                                <h5 class="card-title">{{ $card['title'] }}</h5>
                                <hr>
                                {{--                        <p class="card-text">{{ $card['description'] }}</p>--}}
                                <a href="https://www.marigopharma.it/wp-content/uploads/2018/02/Cat_ProntoLeggo_2023_WEB.pdf" target="_blank" class="custom-link btn btn-md btn-accent btn-outline btn-icon-left btn-block">SCARICA IL CATALOGO <i class="fa fa-download"></i></a>
                                <a href="https://www.marigopharma.it/richiesta-informazioni-prodotti-per-farmacie-e-parafarmacie/" tabindex="-1" class="pushed custom-link btn btn-md btn-accent btn-outline btn-icon-left btn-block">RICHIEDI INFORMAZIONI <i class="fa fa-envelope"></i></a>

                                {{--                        <a href="#" class="btn btn-primary">SCARICA IL CATALOGO</a>--}}
                                {{--                        <a href="#" class="btn btn-secondary">RICHIEDI INFORMAZIONI</a>--}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @include('core/base::layouts.partials.footer')

    </div>
@stop

