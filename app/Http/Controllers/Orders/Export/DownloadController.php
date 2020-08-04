<?php

namespace App\Http\Controllers\Orders\Export;

use App\Exporters\Orders\CsvExporter;
use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Support\Csv\Csv;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class DownloadController extends Controller
{
    protected $basePath;

    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $orders = Order::where('user_id', $userId)
            ->state($request->input('state'))
            ->presale($request->input('presale'))
            ->with([
                'articles.language',
                'articles.card.expansion',
                'buyer',
            ])->get();

        $this->basePath = 'export/' . $userId . '/order';
        $this->makeDirectory($this->basePath);

        return [
            'path' => CsvExporter::all($userId, $orders, $this->basePath . '/orders.csv'),
        ];
    }

    protected function makeDirectory($path)
    {
        Storage::disk('public')->makeDirectory($path);

        return $path;
    }

    protected function saveZip(int $userId, Collection $orders) : string {

        $path = $this->basePath . '/orders.zip';
        $zip = new \ZipArchive();
        $opened = $zip->open(Storage::disk('public')->path($path), \ZipArchive::CREATE|\ZipArchive::OVERWRITE);
        if ($opened === true) {

            $zip->addFile($this->saveOrders($userId, $orders), 'orders.csv');
            $zip->addFile($this->saveByers($userId, $orders), 'buyers.csv');
            $zip->addFile($this->saveArticles($userId, $orders), 'articles.csv');

            $zip->close();

            return Storage::disk('public')->url($path);
        }

        return '';
    }

    protected function saveAll(int $userId, Collection $orders) : string
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
        $path = $this->basePath . '/orders.csv';
        $csv->collection($collection)
            ->header($header)
            ->callback( function($item) {
                return $item;
            })->save(Storage::disk('public')->path($path));

        return Storage::disk('public')->url($path);
    }

    protected function saveCsv(Collection $collection, array $header, string $path)
    {
        $csv = new Csv();
        $csv->collection($collection)
            ->header($header)
            ->callback( function($item) {
                return $item->getAttributes();
            })->save($path);

        return $path;
    }

    protected function saveOrders(int $userId, Collection $orders) : string
    {
        $header = array_keys($orders->first()->getAttributes());
        $path = Storage::disk('public')->path($this->basePath . '/orders.csv');

        return $this->saveCsv($orders, $header, $path);
    }

    protected function saveByers(int $userId, Collection $orders) : string
    {
        $csv = new Csv();
        $path = Storage::disk('public')->path($this->basePath . '/buyers.csv');
        $csv->collection($orders)
            ->header(array_keys($orders->first->buyer->getAttributes()))
            ->callback( function($order) {
                return $order->buyer->getAttributes();
            })->save($path);

        return $path;
    }

    protected function saveArticles(int $userId, Collection $orders) : string
    {
        $articles = new Collection();
        foreach ($orders as $key => $order) {
            foreach ($order->articles as $key => $article) {
                $article->order_id = $order->id;
                $articles->push($article);
            }
        }

        $header = array_keys($articles->first()->getAttributes());
        $path = Storage::disk('public')->path($this->basePath . '/articles.csv');

        return $this->saveCsv($articles, $header, $path);
    }
}
