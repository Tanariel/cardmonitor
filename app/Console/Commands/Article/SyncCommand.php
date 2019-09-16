<?php

namespace App\Console\Commands\Article;

use App\User;
use Illuminate\Console\Command;

class SyncCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add/Update articles from cardmarket API';

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
        $users = User::with('api')->get();
        foreach ($users as $key => $user) {
            if ($user->api->isConnected() === false) {
                continue;
            }

            $this->sync($user);
        }
    }

    protected function sync(User $user)
    {
        $cardmarketArticles = $user->cardmarketApi->stock->get();
        dump($cardmarketArticles);
    }
}
