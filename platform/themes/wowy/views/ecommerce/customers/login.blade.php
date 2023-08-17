@php
    Theme::layout('full-width');
@endphp

<section class="pt-100 pb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 m-auto">
                <div class="login_wrap widget-taber-content p-30 background-white border-radius-10">
                    <div class="padding_eight_all bg-white">
                        <div class="heading_s1 mb-20">
                            <h3 class="mb-20">{{ __('Login') }}</h3>
                            <p>{{ __('Please enter your email address and password') }}</p>
                        </div>
                        <form class="form--auth form--login" method="POST" action="{{ route('customer.login.post') }}">
                            @csrf
                            @if (isset($errors) && $errors->has('confirmation'))
                                <div class="alert alert-danger">
                                    <span>{!! $errors->first('confirmation') !!}</span>
                                </div>
                                <br>
                            @endif
                            <div class="form__content">
                                <div class="form-group">
                                    <label for="txt-email" class="required">{{ __('Email Address') }}</label>
                                    <input name="email" id="txt-email" type="email" value="{{ old('email') }}" placeholder="{{ __('Please enter your email address') }}">
                                    @if ($errors->has('email'))
                                        <span class="text-danger">{{ $errors->first('email') }}</span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="txt-password" class="required">{{ __('Password') }}</label>
                                    <div class="form__password">
                                        <input type="password" name="password" id="txt-password" placeholder="{{ __('Please enter your password') }}">
                                    </div>
                                    @if ($errors->has('password'))
                                        <span class="text-danger">{{ $errors->first('password') }}</span>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="txt-password" class="required">{{ __('Captcha') }}</label>
                                    <div class="row">
                                        <div class="col-12 captcha" style="position: relative">
                                            <div class="captcha-value" style="position: absolute;
                                            top: 1%;
                                            right: 2%;
                                            background: white;
                                            padding: 11px 20px;
                                            font-weight: bold;
                                            border-radius: 42px;
                                            font-size: 13pt;">
                                                <span id="captcha-1">
                                                    {{ mt_rand(1,9) }}
                                                </span>
                                                +
                                                <span id="captcha-2">

                                                    {{ mt_rand(1,9) }}
                                                </span>
                                            </div>
                                            <div class="form__password">
                                                <input type="text" id="captcha-login" placeholder="{{ __('Risultato della somma') }}">
                                            </div>
                                            <span class="text-danger captcha-error"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="login_footer form-group">
                                    <div class="chek-form">
                                        <div class="custome-checkbox">
                                            <input class="form-check-input" type="checkbox" name="remember" id="remember-me-checkbox">
                                            <label class="form-check-label" for="remember-me-checkbox"><span>{{ __('Remember me') }}</span></label>
                                        </div>
                                    </div>
                                    <a class="text-muted" href="{{ route('customer.password.reset') }}">{{ __('Forgot password?') }}</a>
                                </div>
                            </div>
                            <div class="form-group">
                                <button type="submit" class="form--auth--btn btn btn-fill-out btn-block hover-up">{{ __('Login') }}</button>
                            </div>
                            <br>
                            <p>{{ __("Don't have an account?") }} <a href="{{ route('customer.register') }}" class="d-inline-block">{{ __('Create one') }}</a></p>

                            <div class="text-left">
                                {!! apply_filters(BASE_FILTER_AFTER_LOGIN_OR_REGISTER_FORM, null, \Botble\Ecommerce\Models\Customer::class) !!}
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
