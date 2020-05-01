<?php

namespace App\Exporters\Orders;

use App\Support\Csv\Csv;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;

class CsvExporter
{
    public static function all(int $userId, Collection $orders, string $path)
    {
        $firstOrder = $orders->first();
        $firstBuyer = $firstOrder->buyer;
        $firstArticle = $firstOrder->articles->first();

        // $firstOrder->only([]);

        $header = array_merge(array_keys($firstBuyer->getAttributes()), array_keys($firstOrder->getAttributes()), array_keys($firstArticle->getAttributes()));

        $collection = new Collection();
        foreach ($orders as $key => $order) {
            $order_values = array_values($order->getAttributes());
            $buyer_values = array_values($order->buyer->getAttributes());
            foreach ($order->articles as $key => $article) {
                $item = array_merge($buyer_values, $order_values, array_values($article->getAttributes()));
                $collection->push($item);
            }
        }

        $csv = new Csv();
        $csv->collection($collection)
            ->header($header)
            ->callback( function($item) {
                return $item;
            })->save(Storage::disk('public')->path($path));

        return Storage::disk('public')->url($path);
    }
}