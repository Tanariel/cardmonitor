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
        $this->app->singleton('CardmarketApi', function ($app) {
            $access = [
                'app_token' => 'Z03Nl7LJhatTiahP',
                'app_secret' => '9fGXiRe2xwTjFhsNBF2skO7SaxosBgHq',
                'access_token' => 'TSHjoKXWUPv2jRlv6zEtpGY0uc9Kq8BA',
                'access_token_secret' => 'BlHQiYHmdL6v4cuGSMlwsvqv1XCrGtA6',
            ];

            return new Api($access, Api::URL_SANDBOX);
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
