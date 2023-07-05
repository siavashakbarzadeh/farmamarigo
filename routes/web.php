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
Route::prefix('/admin/ecommerce/questionnaires')
    ->name('admin.ecommerce.questionnaires.')
    ->middleware('auth')
    ->group(function (Router $router) {
        $router->get('/', [QuestionnaireController::class, 'questionnaires'])->name('index');
        $router->get('/create', [QuestionnaireController::class, 'create'])->name('create');
        $router->post('/store', [QuestionnaireController::class, 'store'])->name('store');
        $router->post('/check-active-changes', [QuestionnaireController::class, 'checkActiveChanges'])->name('check-active-changes');
        $router->put('/{id}', [QuestionnaireController::class, 'update'])->name('update');
        $router->delete('/{questionnaire}', [QuestionnaireController::class, 'delete'])->name('delete');
        $router->get('/{id}/edit', [QuestionnaireController::class, 'edit'])->name('edit');
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
