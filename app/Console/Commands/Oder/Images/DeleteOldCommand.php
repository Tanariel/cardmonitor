<?php

namespace App\Console\Commands\Oder\Images;

use App\Models\Images\Image;
use App\Models\Orders\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class DeleteOldCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'order:images:delete:old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deletes all images from received orders afer 30 days';

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
        $images = Image::whereHasMorph('imageable', [Order::class], function ($query) {
            return $query->where('state', 'received')
                ->where('received_at', '<=', now()->subDays(Order::DAYS_TO_HAVE_IAMGES));
        })->get();

        $deleted_count = 0;
        foreach ($images as $key => $image) {
            if (Storage::disk('public')->delete('images/' . $image->filename)) {
                $image->delete();
                $deleted_count++;
            }
        }

        $this->line($deleted_count . '/' . $images->count() . ' images deleted');
    }
}
