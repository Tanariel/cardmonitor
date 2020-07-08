<?php

namespace Tests\Feature\Users;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ResetControllerTest extends TestCase
{
    /**
     * @test
     */
    public function it_resets_the_user()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $this->user->update([
            'is_applying_rules' => true,
            'is_syncing_articles' => true,
            'is_syncing_orders' => true,
        ]);

        $this->assertEquals(1, $this->user->is_applying_rules);
        $this->assertEquals(1, $this->user->is_syncing_articles);
        $this->assertEquals(1, $this->user->is_syncing_orders);

        $response = $this->get('/user/reset');

        $response->assertStatus(302);

        $this->user->refresh();

        $this->assertEquals(0, $this->user->is_applying_rules);
        $this->assertEquals(0, $this->user->is_syncing_articles);
        $this->assertEquals(0, $this->user->is_syncing_orders);
    }

}
