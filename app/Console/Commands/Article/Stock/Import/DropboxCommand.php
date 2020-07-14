<?php

namespace App\Console\Commands\Article\Stock\Import;

use Illuminate\Console\Command;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class DropboxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:stock:import:dropbox {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Imports stock from a file from dropbox';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $gameIds = $this->moveFiles();
        foreach ($gameIds as $key => $gameId) {
            $this->call('article:stock:import', [
                'user' => $this->argument('user'),
                'game' => $gameId,
            ]);
        }
    }

    protected function moveFiles() : array
    {
        $this->makeFilesystem();
        $gameIds = [];
        $paths = Storage::disk('dropbox')->allFiles('articles');
        foreach ($paths as $path) {
            $filename = basename($path);
            Storage::put($filename, Storage::disk('dropbox')->get($path));
            $parts = explode('-', $filename);
            $gameIds[] = (int) substr($parts[2], 0, -4);
        }

        return $gameIds;
    }

    protected function makeFilesystem()
    {
        Storage::extend('dropbox', function ($app, $config) {
            $client = new Client(config('services.dropbox.accesstoken'));

            return new Filesystem(new DropboxAdapter($client));
        });
    }


}
