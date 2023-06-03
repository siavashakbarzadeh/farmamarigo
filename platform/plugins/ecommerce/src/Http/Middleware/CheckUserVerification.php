<?php

namespace Botble\Ecommerce\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Session;

class CheckUserVerification
{
    public function handle(Request $request, Closure $next)
    {
//        auth()->user()->markEmailAsVerified();
        auth()->user()->unMarkEmailAsVerified();
        if (auth()->user() && !auth()->user()->hasVerifiedEmail()) {
            return redirect('users/verify');
        }
        return $next($request);
    }
}
