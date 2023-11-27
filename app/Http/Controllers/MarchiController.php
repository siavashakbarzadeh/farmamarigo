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
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/copertina-prontoleggo.jpg', 'title' => 'Prontoleggo – Occhiali da lettura', ],
            ['image' => 'image2.jpg', 'title' => 'Brand Italia – Linea antizanzare, maschere viso e linea arnica', 'description' => 'Description for card 2'],
            ['image' => 'image3.jpg', 'title' => 'Nuvita – Puericultura Leggera', 'description' => 'Description for card 3'],
            ['image' => 'image4.jpg', 'title' => 'Petformance – Articoli per la salute, il benessere e l’igiene di cani e gatti', 'description' => 'Description for card 4'],
            ['image' => 'image5.jpg', 'title' => 'Test Rapidi Professionali e Self Test', 'description' => 'Description for card 4'],
            ['image' => 'image6.jpg', 'title' => 'Mascherine Protettive – FFP2 e Chirurgiche', 'description' => 'Description for card 4'],
            ['image' => 'image2.jpg', 'title' => 'Termoscanner e Pulsossimetri', 'description' => 'Description for card 4'],
            ['image' => 'image2.jpg', 'title' => 'Beautytime – Make up', 'description' => 'Description for card 4'],
            ['image' => 'image2.jpg', 'title' => 'Beautytime – Linea viso e Detersione', 'description' => 'Description for card 4'],
            ['image' => 'image2.jpg', 'title' => 'Beautytime – Accessori', 'description' => 'Description for card 4'],
            ['image' => 'image11.jpg', 'title' => 'Beautytime – Gold rose', 'description' => 'Description for card 11'],
            ['image' => 'image11.jpg', 'title' => 'Beautytime – Smokey eye', 'description' => 'Description for card 11'],
            ['image' => 'image12.jpg', 'title' => 'Beautytime – Travel set', 'description' => 'Description for card 11'],
            ['image' => 'image13.jpg', 'title' => 'Beautytime – Lookrezia', 'description' => 'Description for card 11'],
            ['image' => 'image14.jpg', 'title' => 'Beautytime – Lime personalizzate', 'description' => 'Description for card 11'],
            ['image' => 'image15.jpg', 'title' => 'Pasante – Profilattici', 'description' => 'Description for card 11'],
            ['image' => 'image16.jpg', 'title' => 'Röwo – Compresse caldo freddo', 'description' => 'Description for card 11'],
            ['image' => 'image17.jpg', 'title' => 'Prontoleggo – Sunglasses', 'description' => 'Description for card 11'],
            ['image' => 'image18.jpg', 'title' => 'Card 11', 'description' => 'Description for card 11'],
            // Add more cards as needed
        ];

        return view('Brands.show', compact('cards'));
    }
}
