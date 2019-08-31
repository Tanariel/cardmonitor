<?php

namespace Tests\Feature\Commands\Order;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class SyncCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_syncs_orders()
    {
        $api = App::make('CardmarketApi');

        $api->account->get();

        // $this->artisan('order:sync');
    }
}
