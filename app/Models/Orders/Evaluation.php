<?php

namespace App\Models\Orders;

use App\Models\Orders\Order;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Arr;

class Evaluation extends Model
{
    protected $casts = [
        'complaint' => 'array',
    ];

    protected $guarded = [
        'id',
    ];

    public static function updateOrCreateFromCardmarket(int $orderId, array $cardmarketEvaluation)
    {
        return self::updateOrCreate(['order_id' => $orderId], [
            'grade' => $cardmarketEvaluation['evaluationGrade'],
            'item_description' => $cardmarketEvaluation['itemDescription'],
            'packaging' => $cardmarketEvaluation['packaging'],
            'comment' => $cardmarketEvaluation['comment'],
            'complaint' => Arr::get($cardmarketEvaluation, 'complaint', []),
        ]);
    }

    public function order() : BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
