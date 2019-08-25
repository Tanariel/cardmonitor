<?php

namespace App\Models\Items;

use App\Models\Items\Card;
use App\Models\Items\Quantity;
use App\Models\Items\Transactions\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tightenco\Parental\HasChildren;

class Item extends Model
{
    use HasChildren;

    protected $guarded = [
        'id',
    ];

    /**
     * The booting method of the model.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        static::creating(function($model)
        {
            if (! $model->unit_id) {
                $model->unit_id = 1;
            }

            if (! $model->user_id) {
                $model->user_id = auth()->user()->id;
            }

            return true;
        });
    }

    public function isDeletable() : bool
    {
        return false;
    }

    public function quantity($order) : float
    {
        return 1;
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function quantities() : HasMany
    {
        return $this->hasMany(Quantity::class);
    }

    public static function setup(Model $user)
    {
        $user->items()->create([
            'type' => Card::class,
            'name' => 'Karte',
        ]);

        $user->items()->create([
            'type' => Mailing::class,
            'name' => 'Versandkosten',
        ]);
    }
}
