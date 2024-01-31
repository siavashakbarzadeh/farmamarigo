<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAuthCustomer
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!auth('customer')->user()){
            return redirect('/login');
        }else{
                $user_id = auth('customer')->user()->id;
                $cartRecord = SaveCart::where('user_id', $user_id)->first();
    
                if ($cartRecord) {
                    session(['cart' => $cartRecord->cart]);
                }
                return $next($request);
            }
    
    }
    
}
