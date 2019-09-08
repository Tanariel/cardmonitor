<?php

namespace App;

use App\Models\Apis\Api;
use App\Models\Articles\Article;
use App\Models\Items\Item;
use App\Models\Orders\Order;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
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

    public function setup() : void {
        Item::setup($this);
    }

    public function api()
    {
        return $this->apis()->first();
    }

    public function apis() : HasMany
    {
        return $this->hasMany(Api::class);
    }

    public function articles() : HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function items() : HasMany
    {
        return $this->hasMany(Item::class);
    }

    public function orders() : HasMany
    {
        return $this->hasMany(Order::class);
    }
}
