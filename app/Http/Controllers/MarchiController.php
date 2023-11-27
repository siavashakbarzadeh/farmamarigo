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
use Botble\Ecommerce\Models\ProductCategory;
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
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/evi-brand-italia.jpg', 'title' => 'Brand Italia – Linea antizanzare, maschere viso e linea arnica', 'description' => 'Description for card 2'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/nuvita.jpg', 'title' => 'Nuvita – Puericultura Leggera', 'description' => 'Description for card 3'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/evi-petformance.jpg', 'title' => 'Petformance – Articoli per la salute, il benessere e l’igiene di cani e gatti', 'description' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/test-rapidi.jpg', 'title' => 'Test Rapidi Professionali e Self Test', 'description' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/mix-mascherine.jpg', 'title' => 'Mascherine Protettive – FFP2 e Chirurgiche', 'description' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/img-termoscanner-pusiossimetri-catalogo-marigo.jpg', 'title' => 'Termoscanner e Pulsossimetri', 'description' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/cat-accessories.jpg', 'title' => 'Beautytime – Make up', 'description' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/cat-cosmetics.jpg', 'title' => 'Beautytime – Linea viso e Detersione', 'description' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/cat-make-up.jpg', 'title' => 'Beautytime – Accessori', 'description' => 'Description for card 4'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/cat-rose-gold.jpg', 'title' => 'Beautytime – Gold rose', 'description' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/cat-smokey-eye.jpg', 'title' => 'Beautytime – Smokey eye', 'description' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/cat-travel-set.jpg', 'title' => 'Beautytime – Travel set', 'description' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/cat-hair-accesories.jpg', 'title' => 'Beautytime – Lookrezia', 'description' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/cat-lime.jpg', 'title' => 'Beautytime – Lime personalizzate', 'description' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/copertina-pasante-1.jpg', 'title' => 'Pasante – Profilattici', 'description' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/rowo-compresse-caldo-freddo.jpg', 'title' => 'Röwo – Compresse caldo freddo', 'description' => 'Description for card 11'],
            ['image' => 'https://marigopharma.marigo.collaudo.biz/storage/copertina-prontoleggo-sunglasses.jpg', 'title' => 'Prontoleggo – Sunglasses', 'description' => 'Description for card 11'],

            // Add more cards as needed
        ];

        return view('Brands.show', compact('cards'));
    }
}
