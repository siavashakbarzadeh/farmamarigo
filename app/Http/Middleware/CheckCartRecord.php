<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Botble\Ecommerce\Models\SaveCart;
use Botble\Ecommerce\Models\Product;
use Cart;
use Botble\Ecommerce\Http\Controllers\Customers\LoginController;



class CheckCartRecord
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ($user=$request->user('customer')) {
            $user_id = $user->id;
            $cartRecord = SaveCart::where('user_id', $user_id)->first();

            if ($cartRecord ) {

                dd(Cart::instance('cart')->content()->items == []);

            }
            
        }
        return $next($request);
    }
}