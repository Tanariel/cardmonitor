<?php

namespace Tests\Unit\Models\Users;

use App\Models\Apis\Api;
use App\Models\Articles\Article;
use App\Models\Items\Item;
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

    /**
     * @test
     */
    public function it_has_many_items()
    {
        $model = factory(User::class)->create();
        $related = factory(Item::class)->create([
            'user_id' => $model->id,
        ]);

        $this->assertHasMany($model, $related, 'items');
    }

    /**
     * @test
     */
    public function it_has_many_articles()
    {
        $model = factory(User::class)->create();
        $related = factory(Article::class)->create([
            'user_id' => $model->id,
        ]);

        $this->assertHasMany($model, $related, 'articles');
    }
}
