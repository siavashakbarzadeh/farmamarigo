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
        Cache::forget('VERIFICATION_URL_CUSTOMER_'.auth('customer')->user()->id);
        if (auth('customer')->user() && !auth('customer')->user()->email_verified_at ) {
            
            return redirect('users/verify');
        }
        return $next($request);
    }
}
