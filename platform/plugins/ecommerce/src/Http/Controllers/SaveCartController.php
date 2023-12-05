<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use BaseHelper;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\EmailHandler;
use Botble\Ecommerce\Exports\TemplateProductExport;
use Botble\Ecommerce\Http\Requests\BulkImportRequest;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Imports\ProductImport;
use Botble\Ecommerce\Imports\ValidateProductImport;
use FunctionName;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Excel;
use Hash;
use Illuminate\Support\Facades\Mail;
use Botble\Ecommerce\Mail\Welcome;
use Botble\Ecommerce\Jobs\WelcomeJob;
use Botble\Ecommerce\Models\SaveCart;
use Carbon\Carbon;
use Cart;
use Illuminate\Support\Facades\Session;
use Botble\Ecommerce\Models\OffersDetail;
use Botble\Ecommerce\Models\Offers;
use Botble\Ecommerce\Models\Product;

use Illuminate\Support\Facades\DB;
use RvMedia;







class SaveCartController extends BaseController
{

public static function saveCart($cart,$user_id=null)
{
    $cart=json_encode($cart);
    if($user_id==null){
        $user_id=auth('customer')->user()->id;
    }
    $cartRecord=SaveCart::where('user_id',$user_id)->first();

    if ($cartRecord!=null) {
        //if tooye saveCart ye record ba user_id,expired false nabashe save kone age na hamun ro retrieve kone
        $cartRecord->update([
            'cart' => $cart,
            'updated_at' => Carbon::now()
        ]);

    }else{
        $cartRecord=SaveCart::create([
            'id'=>self::generateRandomID(10),
            'user_id' => $user_id, // You may need to adjust this based on your user authentication logic
            'cart'=> $cart,
            'expired' => false,
            'created_at'=>Carbon::now(),
            'updated_at'=>Carbon::now()
        ]);
    }

}

private static function generateRandomID($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $id = '';

    for ($i = 0; $i < $length; $i++) {
        $id .= $characters[rand(0, strlen($characters) - 1)];
    }

    return $id;
}

public static function deleteSavedCart(){
    $user_id=auth('customer')->user()->id;
    $cartRecord=SaveCart::where('user_id',$user_id)->first();
    if ($cartRecord!=null) {
        $cartRecord->delete();
    }
}

public static function reCalculateCart($user_id) {
    $cartRecord = SaveCart::where('user_id', $user_id)->first();

    if ($cartRecord != null) {
        $cart = json_decode($cartRecord->cart);

        // Clear the current cart instance before re-adding items
        Cart::instance('cart')->destroy();

        if (isset($cart->cart)) {
            foreach ($cart->cart as $item) {
                dd($item);
                $pricelist = DB::connection('mysql')->table('ec_pricelist')
                ->where('customer_id', $user_id)
                ->where('product_id', $item->id)
                ->first();

                // if($pricelist!==null){
                //     $price=$pricelist->final_price;
                // }else{
                //     $price=
                // }

                if ($offerDetail) {
                    $offer = Offers::find($offerDetail->offer_id);
                    if ($offer && in_array($offer->offer_type, [1, 2, 3])) {
                        $price = $offerDetail->product_price;
                    }
                }

                if ($price === null) {
                    $pricelist = DB::connection('mysql')->table('ec_pricelist')
                                    ->where('customer_id', $user_id)
                                    ->where('product_id', $item->id)
                                    ->first();

                    if ($pricelist) {
                        $price = $pricelist->final_price;
                    }
                }

                if ($price !== null) {
                    Cart::instance('cart')->add(
                        $item->id,
                        BaseHelper::clean($item->name),
                        $item->qty,
                        floatval($price),
                        [
                            'image' => RvMedia::getImageUrl($item->options->image, 'thumb', false, RvMedia::getDefaultImage()),
                            'attributes' => '',
                            'taxRate' => $item->options->taxRate,
                            "options" => [],
                            "extras" => []
                        ]
                    );
                }
            }

            // Apply discounts and offers
            foreach (Cart::instance('cart')->content() as $key => $cartItem) {
                // ... Your existing logic for applying discounts and offers ...
            }

            return true; // Or some other success response
        } else {
            return false; // No items in the saved cart
        }
    } else {
        return false; // No saved cart record found
    }
}

public static function addSessionToCart($user_id = null) {
    // Check if the user is logged in. If not, use a default or guest user ID.
    if ($user_id === null) {
        $user_id = auth('customer')->check() ? auth('customer')->user()->id : 'guest';
    }

    // Assuming you have a method to get the current cart (e.g., from session)
    $currentCart = Cart::instance('cart')->content();

    // Loop through the items in the current cart
    foreach ($currentCart as $item) {
        // Check if the product ID exists in the price lists and update the price if necessary
        $pricelist = DB::connection('mysql')->table('ec_pricelist')
                        ->where('customer_id', $user_id)
                        ->where('product_id', $item->id)
                        ->first();

        if ($pricelist) {
            // Update the price in the current item
            $item->price = $pricelist->final_price;
        }

        // Now, add or update this item in the saved cart
        self::addOrUpdateSavedCart($item, $user_id);
    }
}

private static function addOrUpdateSavedCart($item, $user_id) {
    // Retrieve the saved cart for the user
    $cartRecord = SaveCart::where('user_id', $user_id)->first();

    if ($cartRecord) {
        // If there's an existing cart, decode its contents
        $savedCart = json_decode($cartRecord->cart, true);

        // Update or add the item to the saved cart
        $savedCart[$item->id] = [
            'qty' => $item->qty,
            'price' => $item->price,
            // Add other item details as needed
        ];

        // Save the updated cart
        $cartRecord->cart = json_encode($savedCart);
        $cartRecord->save();
    } else {
        // If there's no existing cart, create a new one
        SaveCart::create([
            'id' => self::generateRandomID(10),
            'user_id' => $user_id,
            'cart' => json_encode([$item->id => ['qty' => $item->qty, 'price' => $item->price]]),
            'expired' => false,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now()
        ]);
    }
}


}
