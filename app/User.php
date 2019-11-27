<?php

namespace App;

use App\Models\Apis\Api;
use App\Models\Articles\Article;
use App\Models\Items\Item;
use App\Models\Orders\Order;
use App\Models\Rules\Rule;
use App\Models\Storages\Storage;
use App\Models\Users\Balance;
use App\Support\Users\CardmarketApi;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\App;

class User extends Authenticatable
{
    use Notifiable;

    protected $appends = [
        'balance_in_euro_formatted',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'balance_in_cents',
        'credits',
        'is_applying_rules',
        'is_syncing_articles',
        'is_syncing_orders',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The booting method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::created(function($model)
        {
            $model->setup();

            return true;
        });
    }

    public function getCardmarketApiAttribute() : CardmarketApi
    {
        return new CardmarketApi($this->api);
    }

    public function getBalanceInEuroFormattedAttribute()
    {
        return number_format(($this->balance_in_cents / 100), 2, ',', '.');
    }

    public function setup() : void {
        $this->api()->create();
        Item::setup($this);
    }

    public function api() : HasOne
    {
        return $this->hasOne(Api::class, 'user_id');
    }

    // public function apis() : HasMany
    // {
    //     return $this->hasMany(Api::class);
    // }

    public function articles() : HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function balances() : HasMany
    {
        return $this->hasMany(Balance::class);
    }

    public function items() : HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function orders() : HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function rules() : HasMany
    {
        return $this->hasMany(Rule::class);
    }

    public function storages() : HasMany
    {
        return $this->hasMany(Storage::class);
    }
}
