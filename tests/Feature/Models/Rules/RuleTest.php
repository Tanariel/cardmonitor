<?php

namespace Tests\Feature\Models\Rules;

use App\Models\Articles\Article;
use App\Models\Rules\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\RelationshipAssertions;

class RuleTest extends TestCase
{
    use RelationshipAssertions;

    /**
     * @test
     */
    public function it_has_many_articles()
    {
        $model = factory(Rule::class)->create();
        $related = factory(Article::class)->create([
            'user_id' => $model->user_id,
            'rule_id' => $model->id
        ]);

        $this->assertHasMany($model, $related, 'articles');
    }
}
