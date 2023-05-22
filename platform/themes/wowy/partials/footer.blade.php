
{!! dynamic_sidebar('top_footer_sidebar') !!}
<footer class="main">
    <section class="section-padding-60">
        <div class="container">
            <div class="row">
                {!! dynamic_sidebar('footer_sidebar') !!}
            </div>
        </div>
    </section>
    <div class="container pb-20 wow fadeIn animated">
        <div class="row">
            <div class="col-12 mb-20">
                <div class="footer-bottom"></div>
            </div>
            <div class="col-lg-6">
                <p class="float-md-left font-sm text-muted mb-0">{{ theme_option('copyright') }}</p>
            </div>
            <div class="col-lg-6">
                <p class="text-lg-end text-start font-sm text-muted mb-0">
                    {{ __('All rights reserved.') }}
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Quick view -->
<div class="modal fade custom-modal" id="quick-view-modal" tabindex="-1" aria-labelledby="quick-view-modal-label" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            <div class="modal-body">
                <div class="half-circle-spinner loading-spinner">
                    <div class="circle circle-1"></div>
                    <div class="circle circle-2"></div>
                </div>
                <div class="quick-view-content"></div>
            </div>
        </div>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

@if (is_plugin_active('ecommerce'))
    <script>
        window.currencies = {!! json_encode(get_currencies_json()) !!};
    </script>
@endif

{!! Theme::footer() !!}

<script>
    window.trans = {
        "Views": "{{ __('Views') }}",
        "Read more": "{{ __('Read more') }}",
        "days": "{{ __('days') }}",
        "hours": "{{ __('hours') }}",
        "mins": "{{ __('mins') }}",
        "sec": "{{ __('sec') }}",
        "No reviews!": "{{ __('No reviews!') }}"
    };
</script>

{!! Theme::place('footer') !!}

@if (session()->has('success_msg') || session()->has('error_msg') || (isset($errors) && $errors->count() > 0) || isset($error_msg))
    <script type="text/javascript">
        window.onload = function () {
            @if (session()->has('success_msg'))
            window.showAlert('alert-success', '{{ session('success_msg') }}');
            @endif

            @if (session()->has('error_msg'))
            window.showAlert('alert-danger', '{{ session('error_msg') }}');
            @endif

            @if (isset($error_msg))
            window.showAlert('alert-danger', '{{ $error_msg }}');
            @endif

            @if (isset($errors))
            @foreach ($errors->all() as $error)
            window.showAlert('alert-danger', '{!! BaseHelper::clean($error) !!}');
            @endforeach
            @endif
        };
    </script>
@endif

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
        integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
        crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
        integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
        crossorigin="anonymous"></script>



<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
<script>

    $('.blog-carousel').owlCarousel({
        autoplay:true,
        autoplayTimeout:2000,
        autoplayHoverPause:true,
        stagePadding: 50,/*the little visible images at the end of the carousel*/
        loop:true,
        rtl: false,
        lazyLoad:true,
        margin:10,
        nav:false,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            800:{
                items: 3
            },
            1000:{
                items:3
            },
            1200:{
                items: 3
            }
        }
    })
    $('.featured-brands-carousel').owlCarousel({
        autoplay:true,
        autoplayTimeout:2000,
        autoplayHoverPause:true,
        stagePadding: 50,/*the little visible images at the end of the carousel*/
        loop:true,
        rtl: false,
        lazyLoad:true,
        autoHeight:true,
        margin:-60,
        nav:false,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:2
            },
            800:{
                items: 3
            },
            1000:{
                items:4
            },
            1200:{
                items: 4
            }
        }
    })
    $('.discounted-carousel').owlCarousel({
        autoplay:true,
        autoplayTimeout:2000,
        autoplayHoverPause:true,
        stagePadding: 50,/*the little visible images at the end of the carousel*/
        loop:true,
        rtl: false,
        lazyLoad:true,
        autoHeight:true,
        margin:10,
        nav:false,
        responsive:{
            0:{
                items:1
            },
            600:{
                items:1
            },
            800:{
                items: 1
            },
            1000:{
                items:2
            },
            1200:{
                items: 3
            }
        }
    })

</script>

<script>

    $("#condizioni-generali").click(function(){
        Swal.fire({
            title: '<strong>CONDIZIONI GENERALI DI VENDITA</strong>',
            icon: 'info',
            html:
                `<div style="height:300px;overflow-x:scroll; text-align:left">
    <h4>ORDINI</h4>
    <p>Gli ordini vanno trasmessi direttamente a Marigo Italia attraverso: WEB Log In - Marigo Lab EMAIL ordini@marigoitalia.it TELEFONO (+39) 081 534 46 11 FAX (+39) 081 878 75 84</p>
    <br>
    <p>L’impegno della fornitura si intende sempre nei limiti della disponibilità della merce. Marigo Italia s.r.l. si riserva il diritto, in tutto o in parte, di annullare o differire ordini a seguito di sopravvenute impossibilità di fornitura dovute a causa di forza maggiore che dovessero verificarsi nei nostri magazzini o in quelli dei nostri fornitori. Su ciascun ordine deve essere specificato:</p>
    <p>• Indirizzo cui la merce deve essere inviata</p>
    <p>• Codice cliente che viene riportato in offerta o in fattura</p>
    <p>• Partita I.V.A. / Codice fiscale</p>
    <p>• Numero e data dell’ordine</p>
    <br>
    <p>Per ciascun prodotto ordinato deve essere specificato:</p>
    <p>• Codice</p>
    <p>• La quantità ordinata</p>
    </div>`,
            showCloseButton: true,
            showCancelButton: true,
            focusConfirm: false,
            confirmButtonText:
                '<i class="fa fa-thumbs-up"></i> Perfetto!',
            confirmButtonAriaLabel: 'Thumbs up, great!',
            cancelButtonText:
                '<i class="fa fa-thumbs-down"></i>',
            cancelButtonAriaLabel: 'Thumbs down'
        })
    })

    $("#wishlistAction a").click(function(){
        if($(this).find(".fa-heart").hasClass('fas')){
            $(this).find(".fa-heart").removeClass('fas');
            $(this).find(".fa-heart").addClass('far');
            $(this).find(".fa-heart").css("color","#005BA1");
        }else{
            $(this).find(".fa-heart").removeClass('far');
            $(this).find(".fa-heart").addClass('fas');
            $(this).find(".fa-heart").css("color","red");
        }

    })
</script>
<script>

    $(".form-check-input").click(async function(){
        await $(this).closest('#products-filter-ajax').submit();
        setTimeout( function(){
            location.reload();
        },500);
    })

    $("#search-consumabili").keyup(function(){
        $(this).closest('form').submit();
    });

    $('#wishlist').click(function(){

        $('#queryonsearch').submit();

    });
</script>

<div id="scrollUp"><i class="fal fa-long-arrow-up"></i></div>
</body>
</html>
