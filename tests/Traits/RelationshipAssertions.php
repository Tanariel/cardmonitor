<?php

namespace Tests\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait RelationshipAssertions
{
    public function assertBelongsTo(Model $model, Model $related, string $relationship)
    {
        $this->assertEquals(HasMany::class, get_class($model->$relationship()));

        $this->assertCount(0, $model->fresh()->$relationship);

        $related->$relationship()
            ->associate($model->id)
            ->save();

        $this->assertCount(1, $model->fresh()->$relationship);
    }

    public function assertHasMany(Model $model, Model $related, string $relationship)
    {
        $this->assertEquals(HasMany::class, get_class($model->$relationship()));

        $this->assertCount(1, $model->fresh()->$relationship);
    }

    public function assertHasOne(Model $model, Model $related, string $relationship)
    {
        $this->assertEquals(HasOne::class, get_class($model->$relationship()));
        $this->assertEquals($related->fresh(), $model->fresh()->$relationship);
    }

    public function assertMorphMany(Model $model, string $relatedClass, string $relationship, int $startCount = 0)
    {
        $this->assertEquals(MorphMany::class, get_class($model->$relationship()));

        $this->assertCount($startCount, $model->fresh()->$relationship);

        $model->$relationship()
            ->create(factory($relatedClass)->make()->toArray())
            ->save();

        $this->assertCount(($startCount + 1), $model->fresh()->$relationship);
    }
}

?>