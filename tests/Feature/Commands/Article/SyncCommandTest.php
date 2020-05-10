<?php

namespace Tests\Feature\Commands\Article;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class SyncCommandTest extends TestCase
{
    /**
     * @test
     */
    public function it_syncs_articles()
    {
        $this->markTestSkipped();

        Artisan::call('article:sync', [
            'user' => $this->user->id,
        ]);
    }
}
