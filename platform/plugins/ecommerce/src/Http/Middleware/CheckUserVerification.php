<?php

namespace Botble\Ecommerce\Http\Middleware;

use App\Mail\VerificationAccountMail;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;

class CheckUserVerification
{
    public function handle(Request $request, Closure $next)
    {
        try {
//            $url = URL::signedRoute('customer.user-verify',['id'=>auth('customer')->user()->id],now()->addMinutes(5));
            Mail::to("akbarzadehsiavash@gmail.com")->send(new VerificationAccountMail("salam"));
            dd("salma");
        }catch (\Throwable $e){
            dd($e);
        }

//        auth()->user()->markEmailAsVerified();
//        auth()->user()->unMarkEmailAsVerified();
        if (auth('customer')->user() && !auth('customer')->user()->email_verified_at ) {
            $key = 'VERIFICATION_URL_CUSTOMER_'.auth('customer')->user()->id;

            if (!Cache::has($key)){
                Cache::put($key,"generated",now()->addMinutes(5));
                $url = URL::signedRoute('customer.user-verify',['id'=>auth('customer')->user()->id],now()->addMinutes(5));
                Mail::to(auth('customer')->user()->email)->send(new VerificationAccountMail($url));
            }

            return redirect('users/verify');
        }
        return $next($request);
    }
}
