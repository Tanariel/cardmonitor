<?php

namespace App\Models\Items;

use App\Models\Items\Card;
use App\Models\Items\Quantity;
use App\Models\Items\Transactions\Transaction;
use App\Models\Orders\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Tightenco\Parental\HasChildren;

class Item extends Model
{
    use HasChildren;

    protected $appends = [
        'path',
    ];

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

        static::deleting(function($model)
        {
            $model->quantities()->delete();

            return true;
        });
    }

    public function getPathAttribute()
    {
        return '/item/' . $this->id;
    }

    public function isDeletable() : bool
    {
        return false;
    }

    public function quantity(Order $order) : float
    {
        return $this->quantities()
            ->where(function (Builder $query) use ($order) {
                return $query->whereRaw('? BETWEEN start AND end', [$order->articles_count])
                    ->orWhere(function (Builder $query) use ($order) {
                        return $query->whereNull('end')
                            ->where('start', '<=', $order->articles_count);
                    });
            })
        ->orderBy('start', 'DESC')
        ->first()->quantity ?? 0;
    }

    public function addQuantities(array $quantities, Carbon $effective_from = null)
    {
        $effective_from = $effective_from ?? new Carbon('1970-01-01 00:00:00', 'UTC');
        foreach ($quantities as $quantity => $steps) {
            $this->quantities()->create([
                'effective_from' => $effective_from,
                'end' => $steps['end'],
                'quantity' => $quantity,
                'start' => $steps['start'],
                'user_id' => $this->user_id,
            ]);
        }
    }

    public function transactions() : HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function quantities() : HasMany
    {
        return $this->hasMany(Quantity::class);
    }

    public function scopeSearch($query, $searchtext)
    {
        if ($searchtext == '') {
            return $query;
        }

        return $query->where('name', 'LIKE', '%' . $searchtext . '%');
    }

    public static function setup(Model $user)
    {
        $cards = [
            'Land' => 0.02,
            'Common' => 0.02,
            'Uncommon' => 0.1,
            'Rare' => 0.5,
            'Mystic' => 1,
            'Special' => 0.02,
        ];
        foreach ($cards as $rarity => $unit_cost) {
            Card::create([
                'name' => $rarity,
                'unit_cost' => $unit_cost,
                'user_id' => $user->id,
            ]);
        }
    }
}
