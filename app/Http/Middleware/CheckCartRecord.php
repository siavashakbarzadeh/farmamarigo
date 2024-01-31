<?php
namespace App\Http\Middleware;
use Closure;
use Illuminate\Support\Facades\Auth;
use Botble\Ecommerce\Models\SaveCart;
use Cart;


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

            if ($cartRecord) {
                // Decode the JSON string from the 'cart' column into an array
                $cartData = json_decode($cartRecord->cart, true); // true to get an associative array

                if (json_last_error() === JSON_ERROR_NONE) { // Check if JSON decoding was successful
                    session(['cart' => $cartData]);
                } else {
                    // Handle the error or ignore the cart data if it's not a valid JSON
                }
            }
            dd($cartRecord,session('cart'));

            
        }
        return $next($request);
    }
}