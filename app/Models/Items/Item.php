<?php

namespace App\Models\Items;

use App\Models\Items\Card;
use App\Models\Items\Custom;
use App\Models\Items\Quantity;
use App\Models\Items\Transactions\Transaction;
use App\Models\Orders\Order;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Artisan;
use Parental\HasChildren;

class Item extends Model
{
    use HasChildren;

    const DECIMALS = 6;

    protected $appends = [
        'editPath',
        'isDeletable',
        'isEditable',
        'path',
        'unit_cost_formatted',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'unit_id',
        'unit_cost_formatted',
        'unit_cost',
        'type',
        'stock',
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

    public function setUnitCostFormattedAttribute($value)
    {
        $this->unit_cost = number_format(str_replace(',', '.', $value), self::DECIMALS, '.', '');
        Arr::forget($this->attributes, 'unit_cost_formatted');
    }

    public function getUnitCostFormattedAttribute()
    {
        return number_format($this->unit_cost, 2, ',', '');
    }

    public function getPathAttribute()
    {
        return $this->path('show');
    }

    public function getEditPathAttribute()
    {
        return $this->path('edit');
    }

    protected function path(string $action = '') : string
    {
        return route($this->baseRoute() . '.' . $action, ['item' => $this->id]);
    }

    protected function baseRoute() : string
    {
        return 'item';
    }

    public function getIsEditableAttribute()
    {
        return $this->isEditable();
    }

    public function getIsDeletableAttribute()
    {
        return $this->isDeletable();
    }

    public function isEditable() : bool
    {
        return false;
    }

    public function isDeletable() : bool
    {
        return false;
    }

    public function hasQuantities() : bool
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
        return $this->hasMany(Transaction::class, 'item_id');
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
        Artisan::call('items:create', [
            'user' => $user->id,
        ]);

        $item = Custom::updateOrCreate(['user_id' => $user->id, 'name' => 'Postkarte'], [
            'name' => 'Postkarte',
            'unit_cost' => 0.02,
            'user_id' => $user->id,
        ]);
        $item->quantities()->delete();
        $item->addQuantities([
            1 => [
                'start' => 1,
                'end' => 16,
            ],
        ]);

        $item = Custom::updateOrCreate(['user_id' => $user->id, 'name' => 'Hülle'], [
            'name' => 'Hülle',
            'unit_cost' => 0.02,
            'user_id' => $user->id,
        ]);
        $item->quantities()->delete();
        $item->addQuantities([
            1 => [
                'start' => 1,
                'end' => 4,
            ],
            2 => [
                'start' => 5,
                'end' => 8,
            ],
            3 => [
                'start' => 9,
                'end' => 12,
            ],
            4 => [
                'start' => 13,
                'end' => 16,
            ],
        ]);

        $item = Custom::updateOrCreate(['user_id' => $user->id, 'name' => 'Briefumschlag Din C6'], [
            'name' => 'Briefumschlag Din C6',
            'unit_cost' => 0.0179,
            'user_id' => $user->id,
        ]);
        $item->quantities()->delete();
        $item->addQuantities([
            1 => [
                'start' => 1,
                'end' => 49
            ],
        ]);

        $item = Custom::updateOrCreate(['user_id' => $user->id, 'name' => 'Briefumschlag Din C4'], [
            'name' => 'Briefumschlag Din C4',
            'unit_cost' => 0.199,
            'user_id' => $user->id,
        ]);
        $item->quantities()->delete();
        $item->addQuantities([
            1 => [
                'start' => 50,
                'end' => 500,
            ],
        ]);
    }
}
