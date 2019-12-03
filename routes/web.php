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
    return view('landing.index');
});

Route::get('/impressum', function () {
    return view('impressum');
});

Route::post('/contact', 'ContactController@store');

Route::get('order/{order}/images', 'Images\ImageableController@index')->name('order.images.index');

Route::post('deploy', 'DeploymentController@store');

Auth::routes();

Route::middleware(['auth'])->group(function () {

    Route::get('/cardmarket/create', 'Cardmarket\CallbackController@create')->name('cardmarket.callback.create');
    Route::get('/cardmarket/callback/{request_token}', 'Cardmarket\CallbackController@store')->name('cardmarket.callback.store');
    Route::get('/cardmarket/callback', 'Cardmarket\CallbackController@update')->name('cardmarket.callback.update');
    Route::delete('/cardmarket/callback', 'Cardmarket\CallbackController@destroy')->name('cardmarket.callback.destroy');

    Route::put('/cardmarket/product/{card}', 'Cardmarket\Products\PriceController@update')->name('cardmarket.product.price.update');

    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/home/article', 'Home\Articles\ArticleController@index');
    Route::get('/home/order/month/{year}/{month}', 'Home\Orders\MonthController@index')->name('home.order.month');
    Route::get('/home/order/year/{year}', 'Home\Orders\YearController@index')->name('home.order.year');

    Route::get('article/sync', 'Cardmarket\Articles\ArticleController@index');
    Route::put('article/sync', 'Cardmarket\Articles\ArticleController@update')->name('article.sync.update');

    Route::resource('article', 'Articles\ArticleController');

    Route::resource('card', 'Cards\CardController');

    Route::get('expansion', 'ExpansionController@index');

    Route::post('item/reload', 'Items\ReloadController@store');
    Route::resource('item', 'Items\ItemController');

    Route::resource('image', 'Images\ImageController')->only([
        'index',
        'destroy',
    ]);

    Route::post('order/{order}/images', 'Images\ImageableController@store')->name('order.images.store');

    Route::get('order/sync', 'Cardmarket\Orders\OrderController@index');
    Route::put('order/sync', 'Cardmarket\Orders\OrderController@update')->name('order.sync.update');

    Route::resource('order', 'Orders\OrderController')->except([
        'create',
        'store',
        'delete',
    ]);

    Route::post('order/{order}/send', 'Cardmarket\Orders\SendController@store')->name('order.send.store');
    Route::get('order/{order}/message/create', 'Cardmarket\Orders\MessageController@create')->name('order.message.create');
    Route::post('order/{order}/message', 'Cardmarket\Orders\MessageController@store')->name('order.message.store');
    Route::put('order/{order}/sync', 'Cardmarket\Orders\OrderController@update')->name('order.sync.update');

    Route::post('order/{order}/transactions', 'Orders\TransactionController@store');
    Route::put('order/{order}/transactions/{transaction}', 'Orders\TransactionController@update');
    Route::delete('order/{order}/transactions/{transaction}', 'Orders\TransactionController@destroy');

    Route::get('rule/apply', 'Rules\ApplyController@index');
    Route::post('rule/apply', 'Rules\ApplyController@store');
    Route::put('rule/sort', 'Rules\SortController@update');
    Route::post('rule/{rule}/activate', 'Rules\ActiveController@store');
    Route::delete('rule/{rule}/activate', 'Rules\ActiveController@destroy');
    Route::resource('rule', 'Rules\RuleController');

    Route::post('storages/assign', 'Storages\AssignController@store');
    Route::resource('storages', 'Storages\StorageController');
    Route::put('storages/{storage}/parent', 'Storages\ParentController@update');

    Route::resource('content', 'Storages\ContentController')->except([
        'index',
        'store',
    ]);
    Route::get('storages/{storage}/content', 'Storages\ContentController@index')->name('storage.content.index');
    Route::post('storages/{storage}/content', 'Storages\ContentController@store')->name('storage.content.store');

    Route::resource('transaction', 'Items\Transactions\TransactionController');

    Route::resource('quantity', 'Items\QuantityController')->except([
        'index',
        'store',
    ]);
    Route::get('item/{item}/quantity', 'Items\QuantityController@index')->name('quantity.index');
    Route::post('item/{item}/quantity', 'Items\QuantityController@store')->name('quantity.store');

    Route::get('/user/balance', 'Users\Balances\BalanceController@index');

    Route::get('/user/settings', 'Users\UserController@edit')->name('user.edit');
    Route::put('/user/settings', 'Users\UserController@update')->name('user.update');

});
