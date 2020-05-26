<?php

namespace Tests\Feature\Models\Rules;

use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
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
    public function it_sets_its_order_column()
    {
        $model = factory(Rule::class)->create();
        $this->assertEquals(1, $model->order_column);

        $model = factory(Rule::class)->create([
            'user_id' => $model->user_id
        ]);
        $this->assertEquals(2, $model->order_column);

        $model = factory(Rule::class)->create();
        $this->assertEquals(1, $model->order_column);
    }

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

    /**
     * @test
     */
    public function it_finds_the_rule_for_a_card()
    {
        $expansion = factory(Expansion::class)->create();
        $card = factory(Card::class)->create([
            'expansion_id' => $expansion->id,
            'rarity' => 'rare',
        ]);

        $this->assertNull(Rule::findForCard($this->user->id, $card)->id);

        $rule = factory(Rule::class)->create();

        $this->assertNull(Rule::findForCard($this->user->id, $card)->id);

        $rule = factory(Rule::class)->create([
            'user_id' => $this->user->id,
        ]);

        $this->assertNull(Rule::findForCard($this->user->id, $card)->id);

        $ruleAll = factory(Rule::class)->create([
            'user_id' => $this->user->id,
            'active' => true,
            'order_column' => 1,
        ]);

        $this->assertEquals($ruleAll->id, Rule::findForCard($this->user->id, $card)->id);

        $ruleExpansion = factory(Rule::class)->create([
            'user_id' => $this->user->id,
            'active' => true,
            'expansion_id' => $expansion->id,
            'order_column' => 2,
        ]);

        $this->assertEquals($ruleAll->id, Rule::findForCard($this->user->id, $card)->id);

        $ruleAll->update([
            'order_column' => 2,
        ]);

        $ruleExpansion->update([
            'order_column' => 1,
        ]);

        $this->assertEquals($ruleExpansion->id, Rule::findForCard($this->user->id, $card)->id);
    }

    /**
     * @test
     */
    public function it_can_be_applied()
    {
        $this->markTestSkipped('Sqlite can not join on update');

        $card = factory(Card::class)->create([
            'rarity' => 'rare',
            'price_trend' => 2.00,
        ]);
        $card->refresh();

        $article = factory(Article::class)->create([
            'card_id' => $card->id,
            'user_id' => $this->user->id,
            'unit_price' => 1.00,
            'condition' => 'NM',
        ]);
        $article->refresh();

        $rule = factory(Rule::class)->create([
            'user_id' => $this->user->id,
            'active' => true,
            'base_price' => 'price_trend',
            'multiplier' => 1,
            'price_above' => 0.02,
            'price_below' => 3.00,
            'is_foil' => false,
            'is_signed' => false,
            'is_altered' => false,
            'is_playset' => false,
            'min_price_rare' => 0.5,
            'condition' => 'NM',
            'game_id' => 1,
        ]);
        $rule->refresh();

        $rule->apply($sync = true);

        $article->refresh();

        $this->assertEquals($rule->id, $article->rule_id);
        $this->assertEquals(5, $article->price_rule);
        $this->assertEquals(5, $article->unit_price);
    }
}
