<?php

namespace Tests\Unit\Models\Users;

use App\Models\Apis\Api;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\RelationshipAssertions;

class UserTest extends TestCase
{
    use RelationshipAssertions;

    /**
     * @test
     */
    public function it_has_many_apis()
    {
        $model = factory(User::class)->create();
        $related = factory(Api::class)->create([
            'user_id' => $model->id,
        ]);

        $this->assertHasMany($model, $related, 'apis');
    }
}
