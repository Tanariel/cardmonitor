<?php

namespace App\Providers;

use App\Models\Items\Item;
use App\Models\Orders\Order;
use App\Models\Rules\Rule;
use App\Models\Storages\Storage;
use App\Policies\Items\ItemPolicy;
use App\Policies\Orders\OrderPolicy;
use App\Policies\Rules\RulePolicy;
use App\Policies\Storages\StoragePolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Item::class => ItemPolicy::class,
        Order::class => OrderPolicy::class,
        Rule::class => RulePolicy::class,
        Storage::class => StoragePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
