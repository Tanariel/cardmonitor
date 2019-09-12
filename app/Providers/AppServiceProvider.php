<?php

namespace App\Providers;

use Cardmonitor\Cardmarket\Api;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind('CardmarketApi', function ($app, array $parameters) {
            return new Api($parameters['api']->accessdata);
        });

        $this->app->singleton('SkryfallApi', function ($app, array $parameters) {
            return new \Cardmonitor\Skryfall\Api();
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Validator::extend('formated_number', function ($attribute, $value, $parameters, $validator) {
            return preg_match('/^[0-9]+,?[0-9]*$/', $value);
        });
    }
}
