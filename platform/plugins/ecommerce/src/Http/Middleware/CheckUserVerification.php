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
        // Check if the user is a customer and if their email is not verified
        if (auth('customer')->user() && !auth('customer')->user()->email_verified_at) {
            // If the user is on the verify page or has already been redirected there, don't redirect again
            if (!$request->is('users/verify')) {
                // Store the original intended destination
                $request->session()->put('url.intended', $request->url());

                // Redirect to 'users/verify'
                return redirect('users/verify');
            }
        }

        // Continue with the request if the user is verified or on the verification page
        return $next($request);
    }
}
