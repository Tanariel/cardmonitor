<?php

namespace Tests\Unit\Models\Users;

use App\Models\Users\CardmarketUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CardmarketUserTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_its_lastname()
    {
        $model = factory(CardmarketUser::class)->create([
            'firstname' => 'Firstname',
            'name' => 'Firstname Lastname',
        ]);
        $this->assertEquals('Lastname', $model->lastname);
    }
}
