<?php

namespace Tests\Feature\Models\Rules;

use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use App\Models\Localizations\Language;
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

    /**
     * @test
     */
    public function it_gets_its_price()
    {
        $card = factory(Card::class)->create([
            'price_trend' => 10,
        ]);

        $model = factory(Rule::class)->create([
            'multiplier' => 2,
            'base_price' => 'price_trend',
        ]);

        $this->assertEquals(20, $model->price($card));
    }

    /**
     * @test
     */
    public function it_finds_a_rule_from_attributes()
    {
        $base_price = 123;

        $card = factory(Card::class)->create([
            'price_trend' => $base_price,
        ]);

        $article = factory(Article::class)->create([
            'user_id' => $this->user->id,
            'language_id' => Language::DEFAULT_ID,
            'condition' => 'NM',
            'is_foil' => false,
            'is_altered' => false,
            'is_signed' => false,
            'is_playset' => false,
        ]);

        $attributes = [
            'expansion_id' => $article->card->expansion->id,
            'language_id' => $article->language_id,
            'condition' => $article->condition,
            'is_foil' => $article->is_foil,
            'is_altered' => $article->is_altered,
            'is_signed' => $article->is_signed,
            'is_playset' => $article->is_playset,
            'rarity' => $article->card->rarity,
        ];

        $model = factory(Rule::class)->create([
            'active' => false,
            'user_id' => $this->user->id,
            'expansion_id' => $article->card->expansion->id,
            'condition' => $article->condition,
            'is_foil' => $article->is_foil,
            'is_altered' => $article->is_altered,
            'is_signed' => $article->is_signed,
            'is_playset' => $article->is_playset,
            'rarity' => $article->card->rarity,
        ]);
        $rule = Rule::findForArticle($this->user->id, $attributes);

        $this->assertEquals(null, $rule->id);
        $this->assertEquals(0, $rule->price($card));

        $model->update([
            'active' => true,
        ]);
        $rule = Rule::findForArticle($this->user->id, $attributes);

        $this->assertEquals($model->id, $rule->id);
        $this->assertEquals($base_price, $rule->price($card));

        $model->update([
            'expansion_id' => null,
            'condition' => null,
        ]);

        $rule = Rule::findForArticle($this->user->id, $attributes);

        $this->assertEquals($model->id, $rule->id);
        $this->assertEquals($base_price, $rule->price($card));

        $model->update([
            'is_foil' => true,
        ]);

        $rule = Rule::findForArticle($this->user->id, $attributes);

        $this->assertEquals(null, $rule->id);
        $this->assertEquals(0, $rule->price($card));
    }
}
