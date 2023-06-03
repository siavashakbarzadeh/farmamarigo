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
//        auth()->user()->markEmailAsVerified();
//        auth()->user()->unMarkEmailAsVerified();
        dd(auth('customer')->user());
        if (auth()->user() && !auth()->user()->hasVerifiedEmail()) {
            $key = 'VERIFICATION_URL_USER_'.auth()->user()->id;
            if (!Cache::has($key)){
                Cache::put($key,"generated",now()->addMinutes(2));
                $url = URL::signedRoute('customer.user-verify',['id'=>auth()->user()->id],now()->addMinutes(2));
                Mail::to("akbarzadehsiavash@gmail.com")->send(new VerificationAccountMail($url));
            }
            return redirect('users/verify');
        }
        return $next($request);
    }
}
