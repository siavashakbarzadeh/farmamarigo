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

        // Check if the user is a customer, if their email is not verified, and they are not already on the 'users/verify' page
    if (auth('customer')->check() &&
        !auth('customer')->user()->email_verified_at &&
        !$request->is('users/verify')) {

        // Redirect to 'users/verify' with the email as a query parameter
        // and add an additional parameter to indicate redirection
        return redirect('users/verify')
                ->with('redirected_for_verification', true)
                ->with('email', auth('customer')->user()->email);
    }

    // If the user is on the 'users/verify' page and has been redirected for verification, do not redirect again
    if ($request->is('users/verify') && $request->session()->get('redirected_for_verification')) {
        // Clear the redirection flag
        $request->session()->forget('redirected_for_verification');
        return $next($request);
    }

    // Continue with the request if the user is verified or if none of the above conditions met
    return $next($request);
        }
}
