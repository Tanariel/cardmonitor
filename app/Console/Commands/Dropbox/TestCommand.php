<?php

namespace App\Console\Commands\Dropbox;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Filesystem;
use Spatie\Dropbox\Client;
use Spatie\FlysystemDropbox\DropboxAdapter;

class TestCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dropbox:test {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Tests the dropbox provider.';

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
        $userId = $this->argument('user');
        $user = User::with(['dropbox'])->find($this->argument('user'));
        if (is_null($user->dropbox)) {
            $access_token = config('services.dropbox.accesstoken');
        }
        else {
            $user->dropbox->refresh();
            $access_token = $user->dropbox->token;
        }

        $this->makeFilesystem($access_token);
        $paths = Storage::disk('dropbox')->allFiles('');
        foreach ($paths as $path) {
            $this->line($path);
        }

        return 0;
    }

    protected function makeFilesystem(string $access_token)
    {
        Storage::extend('dropbox', function ($app, $config) use ($access_token) {
            $client = new Client($access_token);

            return new Filesystem(new DropboxAdapter($client));
        });
    }
}
