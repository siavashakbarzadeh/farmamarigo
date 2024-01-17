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

        // Check if the current route is 'users/verify'
        if ($request->is('/verify')) {
            // If it is, just continue with the request without redirecting
            return $next($request);
        }

        // Check if the user is a customer and if their email is not verified
        if (auth('customer')->user() && !auth('customer')->user()->email_verified_at) {
            // Redirect to 'users/verify' if the email is not verified
            
            return redirect('users/verify?'.auth('customer')->user()->email);
        }

        // Continue with the request if the user is verified
        return $next($request);
    }
}
