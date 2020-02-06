<?php

namespace App\Console\Commands\Article;

use App\User;
use Illuminate\Console\Command;

class SyncCommand extends Command
{
    const GAMES = [
        1,
        3,
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'article:sync {--user=}';

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
        try {
            $user = User::with('api')->find($this->option('user'));
            foreach (self::GAMES as $key => $gameId) {
                $user->cardmarketApi->syncAllArticles($gameId);
            }
        }
        catch (\Exception $e) {
            $user->update([
                'is_syncing_articles' => false,
            ]);

            throw $e;
        }
    }
}
