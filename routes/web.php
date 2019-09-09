<?php

use Illuminate\Support\Facades\App;

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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

Route::middleware(['auth'])->group(function () {

    Route::resource('api', 'Apis\ApiController');

    Route::resource('article', 'Articles\ArticleController');

    Route::resource('card', 'Cards\CardController');

    Route::resource('item', 'Items\ItemController');

    Route::resource('image', 'Images\ImageController')->only([
        'index',
        'destroy',
    ]);

    Route::resource('order/{order}/images', 'Images\ImageableController', [
        'as' => 'order',
    ]);

    Route::resource('order', 'Orders\OrderController')->except([
        'create',
        'store',
        'delete',
    ]);

    Route::resource('transaction', 'Items\Transactions\TransactionController')->except([
        'index',
        'store',
    ]);
    Route::get('item/{item}/transaction', 'Items\Transactions\TransactionController@index')->name('transaction.index');
    Route::post('item/{item}/transaction', 'Items\Transactions\TransactionController@store')->name('transaction.store');

    Route::resource('quantity', 'Items\QuantityController')->except([
        'index',
        'store',
    ]);
    Route::get('item/{item}/quantity', 'Items\QuantityController@index')->name('quantity.index');
    Route::post('item/{item}/quantity', 'Items\QuantityController@store')->name('quantity.store');

});
