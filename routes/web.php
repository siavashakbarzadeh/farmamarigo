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
use Botble\Ecommerce\Jobs\OrderSubmittedJob;
use Botble\Ecommerce\Mail\OrderConfirmed;
use Illuminate\Support\Facades\Mail;

\Illuminate\Support\Facades\Route::get('/mail',function (){
    Mail::to($this->order->address->email)->send(new OrderConfirmed($this->order));
    OrderSubmittedJob::dispatch(\Botble\Ecommerce\Models\Order::query()->latest()->first());
});

Route::get('/importP',function(){
    return view('import');
});
Route::get('/importBrand',function(){
    return view('importbrand');
});
Route::get('/importTax',function(){
    return view('importtaxes');
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
