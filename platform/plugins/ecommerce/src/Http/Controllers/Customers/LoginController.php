<?php

namespace Botble\Ecommerce\Http\Controllers\Customers;

use App\Http\Controllers\Controller;
use App\Mail\VerificationAccountMail;
use Botble\ACL\Models\User;
use Botble\ACL\Traits\AuthenticatesUsers;
use Botble\ACL\Traits\LogoutGuardTrait;
use Botble\Ecommerce\Enums\CustomerStatusEnum;
use Botble\Ecommerce\Models\Customer;
use EcommerceHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use SeoHelper;
use Theme;

class LoginController extends Controller
{
    use AuthenticatesUsers, LogoutGuardTrait {
        AuthenticatesUsers::attemptLogin as baseAttemptLogin;
    }

    public string $redirectTo = '/';

    public function __construct()
    {
        $this->middleware('customer.guest', ['except' => ['logout','verify']]);
    }

    public function verify()
    {
        if (is_null(auth('customer')->user())){
            return redirect('/login');
        }
        if (auth('customer')->user() && auth('customer')->user()->email_verified_at){
            return redirect('/');
        }
        if (auth('customer')->user() && !auth('customer')->user()->email_verified_at ) {
            $key = 'VERIFICATION_URL_CUSTOMER_'.auth('customer')->user()->id;
            if (!Cache::has($key)){
                dd(Cache::get());
                Cache::put($key,"generated",now()->addMinutes(5));
                $url = URL::signedRoute('customer.user-verify',['id'=>auth('customer')->user()->id],now()->addMinutes(5));
                Mail::to(auth('customer')->user()->email)->send(new VerificationAccountMail($url));
            }
        }
        return Theme::scope('ecommerce.customers.verify', [], 'plugins/ecommerce::themes.customers.verify')->render();
    }

    public function userVerify($id)
    {

        Mail::to("s.akbarzadeh@m.icoa.it")->send(new VerificationAccountMail(auth()->user()));
//        Mail::to("s.akbarzadeh@m.icoa.it")->send(new VerificationAccountMail(auth()->user()));
//        dd($request->all(),auth()->user()->email);

        $user = Customer::query()->findOrFail($id);
        if (is_null($user->email_verified_at)){
            $user->update(['email_verified_at'=>now()]);
            if (Cache::has('VERIFICATION_URL_CUSTOMER_'.$user->id))
            Cache::forget('VERIFICATION_URL_CUSTOMER_'.$user->id);
        }
        return redirect('/customer/edit-account');

    }

    public function showLoginForm()
    {
        SeoHelper::setTitle(__('Login'));

        Theme::breadcrumb()->add(__('Home'), route('public.index'))->add(__('Login'), route('customer.login'));

        if (! session()->has('url.intended')) {
            if (! in_array(url()->previous(), [route('customer.login'), route('customer.register')])) {
                session(['url.intended' => url()->previous()]);
            }
        }

        return Theme::scope('ecommerce.customers.login', [], 'plugins/ecommerce::themes.customers.login')->render();
    }

    protected function guard()
    {
        return auth('customer');
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to log in and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        $this->sendFailedLoginResponse();
    }

    public function logout(Request $request)
    {
        $this->guard()->logout();

        return $this->loggedOut($request) ?: redirect('/');
    }

    protected function attemptLogin(Request $request)
    {
        if ($this->guard()->validate($this->credentials($request))) {
            $customer = $this->guard()->getLastAttempted();

            if (EcommerceHelper::isEnableEmailVerification() && empty($customer->confirmed_at)) {
                throw ValidationException::withMessages([
                    'confirmation' => [
                        __(
                            'The given email address has not been confirmed. <a href=":resend_link">Resend confirmation link.</a>',
                            [
                                'resend_link' => route('customer.resend_confirmation', ['email' => $customer->email]),
                            ]
                        ),
                    ],
                ]);
            }

            if ($customer->status->getValue() !== CustomerStatusEnum::ACTIVATED) {
                throw ValidationException::withMessages([
                    'email' => [
                        __('Your account has been locked, please contact the administrator.'),
                    ],
                ]);
            }

            return $this->baseAttemptLogin($request);
        }

        return false;
    }
}
