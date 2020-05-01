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

        $header = array_keys($firstBuyer->getAttributes());
        foreach ($firstOrder->getAttributes() as $key => $value) {
            $header[] = $key;
        }
        foreach ($firstArticle->getAttributes() as $key => $value) {
            $header[] = $key;
        }

        $collection = new Collection();
        foreach ($orders as $key => $order) {
            foreach ($order->articles as $key => $article) {
                $item = [];
                foreach ($order->buyer->getAttributes() as $key => $value) {
                    $item[] = $value;
                }
                foreach ($order->getAttributes() as $key => $value) {
                    $item[] = $value;
                }
                foreach ($article->getAttributes() as $key => $value) {
                    $item[] = $value;
                }
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