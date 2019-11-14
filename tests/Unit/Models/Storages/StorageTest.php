<?php

namespace Tests\Unit\Models\Storages;

use App\Models\Articles\Article;
use App\Models\Storages\Content;
use App\Models\Storages\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\RelationshipAssertions;

class StorageTest extends TestCase
{
    use RelationshipAssertions;

    /**
     * @test
     */
    public function it_has_many_articles()
    {
        $model = factory(Storage::class)->create();
        $related = factory(Article::class)->create([
            'user_id' => $model->user_id,
            'storage_id' => $model->id
        ]);

        $this->assertHasMany($model, $related, 'articles');
    }

    /**
     * @test
     */
    public function it_has_many_contents()
    {
        $model = factory(Storage::class)->create();
        $related = factory(Content::class)->create([
            'user_id' => $model->user_id,
            'storage_id' => $model->id
        ]);

        $this->assertHasMany($model, $related, 'contents');
    }
}
