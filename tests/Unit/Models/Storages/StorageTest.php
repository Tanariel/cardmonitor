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
    public function it_sets_its_full_name()
    {
        $parent = factory(Storage::class)->create([
            'user_id' => $this->user->id,
        ]);
        $child = factory(Storage::class)->create([
            'user_id' => $this->user->id,
        ]);

        $child2 = factory(Storage::class)->create([
            'user_id' => $this->user->id,
        ]);

        $child->appendToNode($parent)
            ->save();

        $child2->appendToNode($child)
            ->save();

        $this->assertEquals($parent->name, $parent->full_name);
        $this->assertEquals($parent->name . '/' . $child->name, $child->full_name);
        $this->assertEquals($parent->name . '/' . $child->name . '/' . $child2->name, $child2->full_name);
    }

    /**
     * @test
     */
    public function it_can_set_its_descendants_full_names()
    {
        $parent = factory(Storage::class)->create([
            'user_id' => $this->user->id,
            'name' => 'parent',
        ]);

        $child = factory(Storage::class)->create([
            'user_id' => $this->user->id,
            'name' => 'child 1',
        ]);

        $child2 = factory(Storage::class)->create([
            'user_id' => $this->user->id,
            'name' => 'child 2',
        ]);

        $child->appendToNode($parent)
            ->save();

        $child2->appendToNode($child)
            ->save();

        $newName = 'New Parent Name';
        $parent->update([
            'name' => $newName,
        ]);

        $child = $child->fresh();
        $child2 = $child2->fresh();

        $this->assertEquals($newName . '/' . $child->name, $child->full_name);
        $this->assertEquals($newName . '/' . $child->name . '/' . $child2->name, $child2->full_name);
    }

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
