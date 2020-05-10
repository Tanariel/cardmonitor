<?php

namespace Tests\Feature\Commands\Card;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SyncCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_syncs_products()
    {
        $this->markTestSkipped();

        Artisan::call('card:sync');
    }
}
