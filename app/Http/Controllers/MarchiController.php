<?php

namespace App\Http\Controllers;

use Botble\Ecommerce\Models\Order;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Botble\Ecommerce\Models\Customer;
use Botble\Ecommerce\Models\Agent;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductTag;
use Botble\Ecommerce\Models\Regione;
use Botble\Ecommerce\Models\Offers;
use Botble\Ecommerce\Models\OffersDetail;
use Botble\Ecommerce\Models\offerType;
use Botble\Ecommerce\Models\PriceList;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use App\Jobs\OfferDeactivationJob;
use Carbon\Carbon;
//use LDAP\Result;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Response;
use Throwable;


class MarchiController extends BaseController
{
    public function index()
    {
        $cards = [
            ['image' => 'image1.jpg', 'title' => 'Card 1', 'description' => 'Description for card 1'],
            ['image' => 'image2.jpg', 'title' => 'Card 2', 'description' => 'Description for card 2'],
            ['image' => 'image2.jpg', 'title' => 'Card 3', 'description' => 'Description for card 3'],
            ['image' => 'image2.jpg', 'title' => 'Card 4', 'description' => 'Description for card 4'],
            // Add more cards as needed
        ];

        return view('Brands.show', compact('cards'));
    }
}
