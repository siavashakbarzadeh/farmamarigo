<?php
use Closure;
use Illuminate\Support\Facades\Auth;
use Botble\Ecommerce\Models\SaveCart;

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
                session(['cart' => $cartRecord->cart]);
            }
        }

        return $next($request);
    }
}