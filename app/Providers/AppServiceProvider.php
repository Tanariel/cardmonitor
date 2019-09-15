<?php

namespace App\Providers;

use Carbon\Carbon;
use Cardmonitor\Cardmarket\Api;
use Illuminate\Support\Arr;
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

            $access = [
                'app_token' => config('app.cardmarket_api.app_token'),
                'app_secret' => config('app.cardmarket_api.app_secret'),
                'url' => ($this->app->environment() == 'production' ? Api::URL_Api : Api::URL_SANDBOX),
            ];

            if (Arr::has($parameters, 'api')) {
                $access += $parameters['api']->accessdata;
            }

            return new Api($access);
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

        Carbon::setLocale('de');
    }
}
