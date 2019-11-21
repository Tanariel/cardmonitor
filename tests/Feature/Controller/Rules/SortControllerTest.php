<?php

namespace Tests\Feature\Controller\Rules;

use App\Models\Rules\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SortControllerTest extends TestCase
{
    protected $baseRouteName = 'rule';
    protected $baseViewPath = 'rule';
    protected $className = Rule::class;

    /**
     * @test
     */
    public function it_can_set_the_order_column()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $models = factory($this->className, 3)->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertDatabaseHas('rules', [
            'id' => 1,
            'order_column' => 1,
        ]);

        $this->assertDatabaseHas('rules', [
            'id' => 2,
            'order_column' => 2,
        ]);

        $this->assertDatabaseHas('rules', [
            'id' => 3,
            'order_column' => 3,
        ]);

        $ranks = [3,2,1];

        $response = $this->put('rule/sort', [
            'rules' => $ranks
        ]);

        $this->assertDatabaseHas('rules', [
            'id' => 1,
            'order_column' => 3,
        ]);

        $this->assertDatabaseHas('rules', [
            'id' => 2,
            'order_column' => 2,
        ]);

        $this->assertDatabaseHas('rules', [
            'id' => 3,
            'order_column' => 1,
        ]);
    }
}
