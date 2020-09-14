<?php

namespace App\Http\Controllers\Orders\Export;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Support\Csv\Csv;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxController extends Controller
{
    protected $basePath;
    protected $filesystem;

    public function __construct()
    {
        $this->makeFilesystem();
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $userId = $user->id;

        Artisan::queue('order:export:dropbox', [
            'user' => $userId,
        ]);

        return back()->with('status', [
            'type' => 'success',
            'text' => 'Bestellungen werden im Hintergrund zu Dropbox exportiert.',
        ]);
    }

    protected function makeFilesystem()
    {
        Storage::extend('dropbox', function ($app, $config) {
            $client = new Client(config('services.dropbox.accesstoken'));

            return new Filesystem(new DropboxAdapter($client));
        });
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $orders = Order::where('user_id', $userId)
            ->with([
                'articles',
                'buyer',
            ])->get();

        $this->basePath = 'export/' . $userId . '/order';
        $this->makeDirectory('orders');

        $this->saveOrders($userId, $orders);
        $this->saveByers($userId, $orders);
        $this->saveArticles($userId, $orders);



        return [
            'path' => $this->saveZip($userId, $orders),
        ];
    }

    protected function makeDirectory($path)
    {
        Storage::disk('public')->makeDirectory($path);

        return $path;
    }

    protected function saveCsv(Collection $collection, array $header, string $path) : File
    {
        $csv = new Csv();
        $csv->collection($collection)
            ->header($header)
            ->callback( function($item) {
                return $item->getAttributes();
            })->save($path);

        return new File($path);
    }

    protected function saveOrders(int $userId, Collection $orders) : File
    {
        $header = array_keys($orders->first()->getAttributes());
        $path = Storage::disk('public')->path($this->basePath . '/orders.csv');

        return $this->saveCsv($orders, $header, $path);
    }

    protected function saveArticles(int $userId, Collection $orders) : File
    {
        $articles = new Collection();
        foreach ($orders as $key => $order) {
            foreach ($order->articles as $key => $article) {
                $articles->push($article);
            }
        }

        $header = array_keys($articles->first()->getAttributes());
        $path = Storage::disk('public')->path($this->basePath . '/articles.csv');

        return $this->saveCsv($articles, $header, $path);
    }

    protected function saveByers(int $userId, Collection $orders) : File
    {
        $csv = new Csv();
        $path = Storage::disk('public')->path($this->basePath . '/buyers.csv');
        $csv->collection($orders)
            ->header(array_keys($orders->first->buyer->getAttributes()))
            ->callback( function($order) {
                return $order->buyer->getAttributes();
            })->save($path);

        return new File($path);
    }
}
