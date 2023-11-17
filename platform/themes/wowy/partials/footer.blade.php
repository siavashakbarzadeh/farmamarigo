
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
            <div class="col-lg-3"><a href="/cookie-policy">Politica Sui Cookie E Sulla Privacy</a></div>
            <div class="col-lg-3">
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

    // $("#owl-demo").owlCarousel({
    //
    //     autoPlay: 3000, //Set AutoPlay to 3 seconds
    //
    //     items : 4,
    //     itemsDesktop : [1199,3],
    //     itemsDesktopSmall : [979,3]
    //
    // });
    // $('.brands-carousel').owlCarousel({
    //     autoPlay: 3000, //Set AutoPlay to 3 seconds
    //         items : 4,
    //         itemsDesktop : [1199,3],
    //         itemsDesktopSmall : [979,3]
    // })
    $('.featured-brands-carousel').owlCarousel({
        autoplay:true,
        autoplayTimeout:2000,
        autoplayHoverPause:true,
        stagePadding: 50,/*the little visible images at the end of the carousel*/
        loop:true,
        rtl: false,
        lazyLoad:true,
        autoHeight:true,
        margin:-50,
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


</script>

<script>

    $(".form--auth--btn").click(function(e){
        e.preventDefault();
        let captchaInput = $("#captcha-login").val();

        axios.post('/captcha-validator/login', { captcha: captchaInput })
        .then(response => {
            if(response.data.valid){
                $('.captcha-error').html('');
                $(".form--auth").submit();

            }
        })
        .catch(error => {
            if(error.response && error.response.status === 422){
                $('.captcha-error').html('La somma inserita non è corretta');
                refreshLoginForm();
            }
        });

    });


    $("#contact-form-btn").click(function(e){
        e.preventDefault();

        let captchaInput = $("#captcha-contact").val();

        axios.post('/captcha-validator/contact-form', { captcha: captchaInput })
            .then(response => {
                if(response.data.valid){
                    $('.captcha-error').html('');

                }
            })
            .catch(error => {
                if(error.response && error.response.status === 422){
                    $('.captcha-error').html('La somma inserita non è corretta');
                    refreshContactForm();
                }
            });
    });
    
    function refreshContactForm() {
        axios.get('/refresh-captcha/contact-form')
            .then(response => {
                // Update the CAPTCHA image source with the new data URI
                $('.captcha-value img').attr('src', response.data.dataUri);
            })
            .catch(error => {
                console.error('Failed to refresh CAPTCHA', error);
            });
    }


    $(".register--btn--submit").click(function(e) {
        e.preventDefault();
    
        // CAPTCHA validation
        let captchaInput = $("#captcha-register").val();
        axios.post('/captcha-validator/register', { captcha: captchaInput })
            .then(response => {
                if(response.data.valid){
                    // If CAPTCHA and all other validations are passed, submit the form
                    $('.form--auth').submit();
                } else {
                    // Handle CAPTCHA validation failure
                    $('.captcha-error').html('Invalid CAPTCHA');
                }
            })
            .catch(error => {
                if(error.response && error.response.status === 422){
                    // Handle specific error here for CAPTCHA
                    $('.captcha-error').html('CAPTCHA is not correct');
                    refreshRegisterFormCaptcha();
                }
            });
    
    });
    
    function refreshRegisterFormCaptcha() {
        axios.get('/refresh-captcha/register')
            .then(response => {
                // Update the CAPTCHA image source with the new data URI
                $('.captcha-value img').attr('src', response.data.dataUri);
            })
            .catch(error => {
                console.error('Failed to refresh CAPTCHA', error);
            });
    }


</script>



<div id="scrollUp"><i class="fal fa-long-arrow-up"></i></div>
</body>
</html>
