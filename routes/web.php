<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

use App\Http\Controllers\MarchiController;
use Botble\Ecommerce\Jobs\OrderSubmittedJob;
use Botble\Ecommerce\Mail\OrderConfirmed;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\SPCController;
use App\Http\Controllers\CreaSconto;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\CaptchaHandler;



use App\Http\Controllers\QuestionnaireController;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;
\Illuminate\Support\Facades\Route::get('/import', function () {
    $products = \Botble\Ecommerce\Models\Product::all();
    try {
        return \Illuminate\Support\Facades\DB::transaction(function () {
            $items = \Botble\Ecommerce\Models\Product::all();
            foreach ($items as $item) {
                $item = collect($item)
                    ->put('u_id', $item->id)
                    ->forget(['id', 'original_price','created_at','updated_at', 'front_sale_price', 'product_collections','variation_info'])
                    ->mapWithKeys(function ($item, $key) {
                        if (is_object($item) && method_exists($item, 'getValue')){
                            $item = $item->getValue();
                        }elseif (is_array($item)){
                            $item =collect($item)->toJson();
                        }
                        return [$key => $item];
                    })->toArray();
                \Illuminate\Support\Facades\DB::connection('farma2')
                    ->table('ec_products')
                    ->updateOrInsert([
                        'u_id' => $item['u_id'],
                    ], $item);
            }
        });
    } catch (Throwable $e) {
        dd($e);
    }
});

Route::get('/importP', function () {
    return view('import');
});
Route::get('/importBrand', function () {
    return view('importbrand');
});
Route::get('/importTax', function () {
    return view('importtaxes');
});
//Route::get('/marchi', function () {
//    return view('Brands.show');
//});
//Route::get('/marchi', [MarchiController::class, 'index']);
// Route::prefix('/admin/ecommerce/questionnaires')
//     ->name('admin.ecommerce.questionnaires.')
//     ->middleware('auth')
//     ->group(function (Router $router) {
//         $router->get('/', [QuestionnaireController::class, 'questionnaires'])->name('index');
//         $router->get('/create', [QuestionnaireController::class, 'create'])->name('create');
//         $router->post('/store', [QuestionnaireController::class, 'store'])->name('store');
//         $router->post('/check-active-changes', [QuestionnaireController::class, 'checkActiveChanges'])->name('check-active-changes');
//         $router->put('/{id}', [QuestionnaireController::class, 'update'])->name('update');
//         $router->delete('/{questionnaire}', [QuestionnaireController::class, 'delete'])->name('delete');
//         $router->get('/{id}/edit', [QuestionnaireController::class, 'edit'])->name('edit');
//         $router->put('/{questionnaire}/active', [QuestionnaireController::class, 'active'])->name('active');
//         $router->put('/{questionnaire}/inactive', [QuestionnaireController::class, 'inactive'])->name('inactive');
//   });
// Route::get('/questionindex', [QuestionnaireController::class, 'index'])
//     ->middleware(['check.auth.customer'])
//     ->name('questionary.index');
// Route::get('/questionnaire/thank-you', [QuestionnaireController::class, 'thankYou'])
//     ->middleware(['check.auth.customer'])
//     ->name('questionnaire.thank-you');
// Route::post('/saveanswer', [QuestionnaireController::class, 'saveAnswers'])
//     ->middleware(['check.auth.customer'])
//     ->name('questionary.save-answers');
Route::prefix('/admin/ecommerce/questionnaires')
    ->name('admin.ecommerce.questionnaires.')
    ->middleware('auth')
    ->group(function (Router $router) {
        $router->get('/', [QuestionnaireController::class, 'questionnaires'])->name('index');
        $router->get('/create', [QuestionnaireController::class, 'create'])->name('create');
        $router->post('/store', [QuestionnaireController::class, 'store'])->name('store');
        $router->post('/check-active-changes', [QuestionnaireController::class, 'checkActiveChanges'])->name('check-active-changes');
        $router->get('/ajax/users', [QuestionnaireController::class, 'ajaxUsers'])->name('ajax-users');
        $router->get('/{id}', [QuestionnaireController::class, 'show'])->name('show');
        $router->put('/{id}', [QuestionnaireController::class, 'update'])->name('update');
        $router->delete('/{questionnaire}', [QuestionnaireController::class, 'delete'])->name('delete');
        $router->get('/{id}/edit', [QuestionnaireController::class, 'edit'])->name('edit');
        $router->post('/{id}/send-email/customers', [QuestionnaireController::class, 'sendEmailToCustomers'])->name('send-email-to-customers');
        $router->put('/{questionnaire}/active', [QuestionnaireController::class, 'active'])->name('active');
        $router->put('/{questionnaire}/inactive', [QuestionnaireController::class, 'inactive'])->name('inactive');

    });
Route::get('/questionindex', [QuestionnaireController::class, 'index'])
    ->middleware(['check.auth.customer'])
    ->name('questionary.index');
Route::get('/questionnaire/thank-you', [QuestionnaireController::class, 'thankYou'])
    ->middleware(['check.auth.customer'])
    ->name('questionnaire.thank-you');
Route::post('/saveanswer', [QuestionnaireController::class, 'saveAnswers'])
    ->middleware(['check.auth.customer'])
    ->name('questionary.save-answers');


    Route::post('/admin/spc_store', [SPCController::class, 'store']);
    Route::post('/admin/spc_deactive', [SPCController::class, 'deactive']);
    Route::post('/spc_apply', [SPCController::class, 'applyCoupon']);
    Route::post('/spc_remove', [SPCController::class, 'removeCoupon']);



        Route::prefix('/admin/ecommerce/spedizione')
        ->name('admin.ecommerce.spedizione.')
        ->group(function (Router $router) {

            $router->get('/view', [SPCController::class, 'getSpedizioneView'])->name('view');
            $router->get('/list', [SPCController::class, 'getSpedizioneListView'])->name('list');
            $router->get('/create-offer', [SPCController::class, 'createOfferView'])->name('create-offer');
            $router->post('/customers_export', [SPCController::class, 'customersExport'])->name('customers_export');
            $router->post('/delete', [SPCController::class, 'delete'])->name('delete');
            $router->post('/update', [CreaSconto::class, 'spedizioneUpdate'])->name('update');
            Route::get('/shipping-form', [ShippingController::class, 'showForm'])->name('shipping.form');
            Route::post('/calculate-shipping', [ShippingController::class, 'calculateShipping'])->name('calculate.shipping');


        });

        Route::prefix('/captcha-validator')->group(function (Router $router) {

            $router->post('/contact-form', [CaptchaHandler::class, 'validateContactForm'])->name('validateContactForm');
            $router->post('/register', [CaptchaHandler::class, 'validateRegisterForm'])->name('validateRegisterForm');
            $router->post('/login', [CaptchaHandler::class, 'validateLoginForm'])->name('validateLoginForm');



        });

        Route::prefix('/refresh-captcha')->group(function (Router $router) {

            $router->get('/contact-form', [CaptchaHandler::class, 'refreshContactForm'])->name('refreshContactForm');
            $router->get('/register', [CaptchaHandler::class, 'refreshRegisterForm'])->name('refreshRegisterForm');
            $router->get('/login', [CaptchaHandler::class, 'refreshLoginForm'])->name('refreshLoginForm');



        });

// Route::get('/pricelist', [\App\Http\Controllers\PricelistController::class, 'pricelist'])
    // ->middleware(['check.auth.customer'])
    // ->name('pricelist.index');
Route::get('/checksession', [QuestionnaireController::class, 'checksession']);

