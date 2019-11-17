<?php

namespace App\Console\Commands\Article;

use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DownloadCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:download {--user=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Download of stockfile for user';

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
        $user = User::with('api')->find($this->option('user'));
        $filename = $user->id . '-stock.csv';

        $data = $user->cardmarketApi->stock->csv();
        $zippedFilename = $filename . '.gz';
        $created = Storage::disk('local')->put($zippedFilename, base64_decode($data['stock']));

        if ($created) {
            dump('gunzip ' . storage_path('app/' . $filename));
            shell_exec('gunzip ' . storage_path('app/' . $filename));
            $this->info('Downloaded ' . $filename);

            return;
        }

        $this->error('Something went wrong!');
    }
}
