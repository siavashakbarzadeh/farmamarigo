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

Route::get('/importDbfrom',function(){
    return view('import');
});
Route::get('/importBrand',function(){
    return view('importbrand');
});
