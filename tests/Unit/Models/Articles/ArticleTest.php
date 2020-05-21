<?php

namespace Tests\Unit\Models\Articles;

use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Localizations\Language;
use App\Models\Orders\Order;
use App\Models\Rules\Rule;
use App\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;
use Tests\Traits\AttributeAssertions;
use Tests\Traits\RelationshipAssertions;

class ArticleTest extends TestCase
{
    use AttributeAssertions, RelationshipAssertions;

    /**
     * @test
     */
    public function it_sets_bought_at_from_formated_value()
    {
        $this->assertSetsFormattedDatetime(Article::class, 'bought_at');
    }

    /**
     * @test
     */
    public function it_sets_sold_at_from_formated_value()
    {
        $this->assertSetsFormattedDatetime(Article::class, 'sold_at');
    }

    /**
     * @test
     */
    public function it_sets_unit_cost_from_formated_value()
    {
        $this->assertSetsFormattedNumber(Article::class, 'unit_cost');
    }

    /**
     * @test
     */
    public function it_sets_unit_price_from_formated_value()
    {
        $this->assertSetsFormattedNumber(Article::class, 'unit_price');
    }

    /**
     * @test
     */
    public function it_sets_provision_from_formated_value()
    {
        $this->assertSetsFormattedNumber(Article::class, 'provision');
    }

    /**
     * @test
     */
    public function it_belongs_to_rule()
    {
        $rule = factory(Rule::class)->create();

        $model = factory(Article::class)->create([
            'rule_id' => $rule->id,
        ]);
        $this->assertEquals(BelongsTo::class, get_class($model->rule()));
    }

    /**
     * @test
     */
    public function it_belongs_to_a_card()
    {
        $model = factory(Article::class)->create([
            'language_id' => Language::DEFAULT_ID,
        ]);
        $this->assertEquals(BelongsTo::class, get_class($model->language()));
    }

    /**
     * @test
     */
    public function it_belongs_to_a_language()
    {
        $card = factory(Card::class)->create();

        $model = factory(Article::class)->create([
            'card_id' => $card->id,
            'language_id' => Language::DEFAULT_ID,
        ]);
        $this->assertEquals(BelongsTo::class, get_class($model->card()));
    }

    /**
     * @test
     */
    public function it_belongs_to_many_orders()
    {
        $parent = factory(Order::class)->create();
        $model = factory(Article::class)->create();
        $this->assertEquals(BelongsToMany::class, get_class($model->orders()));
    }

    /**
     * @test
     */
    public function it_belongs_to_an_user()
    {
        $model = factory(Article::class)->create([
            'user_id' => factory(User::class)->create()->id,
        ]);
        $this->assertEquals(BelongsTo::class, get_class($model->user()));
    }

    /**
     * @test
     */
    public function it_has_a_localized_name()
    {
        $card = factory(Card::class)->create();

        $model = factory(Article::class)->create([
            'card_id' => $card->id,
            'language_id' => Language::DEFAULT_ID,
        ]);

        $this->assertEquals($card->localizations()->where('language_id', Language::DEFAULT_ID)->first()->name, $model->localName);
    }

    /**
     * @test
     */
    public function it_can_be_reindexed()
    {
        $cardmarket_article_id = 1;
        $card = factory(Card::class)->create();

        factory(Article::class, 3)->create([
            'cardmarket_article_id' => $cardmarket_article_id,
            'index' => 1,
            'card_id' => $card->id,
        ]);

        $affected = Article::reindex($cardmarket_article_id);
        $this->assertEquals(3, $affected);

        $collection = Article::where('cardmarket_article_id', $cardmarket_article_id)->orderBy('index', 'ASC')->get();

        $index = 1;
        foreach ($collection as $model) {
            $this->assertEquals($index, $model->index);
            $index++;
        };
    }

    /**
     * @test
     */
    public function it_calculates_its_provision()
    {
        $model = new Article();
        $model->unit_price = 1;
        $this->assertEquals(0.05, $model->provision);

        $model->unit_price = 0.02;
        $this->assertEquals(0.01, $model->provision);

        $model->unit_price_formatted = 0.44;
        $this->assertEquals(0.03, $model->provision);

        $model->unit_price_formatted = 1;
        $this->assertEquals(0.05, $model->provision);
    }

    /**
     * @test
     */
    public function it_can_be_transformed_to_cardmarket()
    {
        $model = factory(Article::class)->create();
        $cardmarketModel = $model->toCardmarket();

        $this->assertArrayHasKey('idLanguage', $cardmarketModel);
        $this->assertArrayHasKey('comments', $cardmarketModel);
        $this->assertArrayHasKey('count', $cardmarketModel);
        $this->assertArrayHasKey('price', $cardmarketModel);
        $this->assertArrayHasKey('condition', $cardmarketModel);
        $this->assertArrayHasKey('isFoil', $cardmarketModel);
        $this->assertArrayHasKey('isSigned', $cardmarketModel);
        $this->assertArrayHasKey('isPlayset', $cardmarketModel);
    }

    /**
     * @test
     */
    public function it_sets_rarity_sort()
    {
        $model = new Article([
            'condition' => 'NM',
        ]);

        $this->assertEquals(5, $model->condition_sort);
    }

    /**
     * @test
     */
    public function it_handles_invalid_condition()
    {
        $model = new Article([
            'condition' => 'Ungueltig',
        ]);

        $this->assertEquals(0, $model->condition_sort);
    }

    /**
     * @test
     */
    public function it_gets_articles_grouped_by_cardmarket_article_id()
    {
        factory(Article::class, 3)->create([
            'user_id' => $this->user->id,
            'cardmarket_article_id' => 123,
        ]);

        $articles = Article::stock()
            ->where('user_id', $this->user->id)
            ->get();

        $this->assertCount(1, $articles);
        $this->assertEquals($this->user->id, $articles->first()->user_id);
        $this->assertEquals('3', $articles->first()->amount);
    }

    /**
     * @test
     */
    public function it_gets_its_amount()
    {
        $model = factory(Article::class)->create();
        $model->refresh();

        $this->assertEquals(Article::count(), $model->amount);

        $model = $model->copy();

        $this->assertEquals(Article::count(), $model->amount);

        $model = $model->copy();

        $this->assertEquals(Article::count(), $model->amount);
    }

    /**
     * @test
     */
    public function it_can_be_copied()
    {
        $oldModel = factory(Article::class)->create();
        $oldModel->refresh();
        $newModel = $oldModel->copy();

        $this->assertCount(2, Article::all());

        $this->assertEquals($newModel->card_id, $oldModel->card_id);
        $this->assertEquals($newModel->cardmarket_article_id, $oldModel->cardmarket_article_id);
        $this->assertEquals($newModel->cardmarket_comments, $oldModel->cardmarket_comments);
        $this->assertEquals($newModel->condition, $oldModel->condition);
        $this->assertEquals($newModel->is_foil, $oldModel->is_foil);
        $this->assertEquals($newModel->is_playset, $oldModel->is_playset);
        $this->assertEquals($newModel->is_signed, $oldModel->is_signed);
        $this->assertEquals($newModel->language_id, $oldModel->language_id);
        $this->assertEquals($newModel->storage_id, $oldModel->storage_id);
        $this->assertEquals($newModel->unit_cost, $oldModel->unit_cost);
        $this->assertEquals($newModel->unit_price, $oldModel->unit_price);
        $this->assertEquals($newModel->user_id, $oldModel->user_id);
    }

    /**
     * @test
     */
    public function it_can_set_its_amount()
    {
        $model = factory(Article::class)->create();
        $model->refresh();

        $newAmount = 3;
        $affected = 2;

        $result = $model->setAmount($newAmount, $sync = false);
        $this->assertCount($newAmount, Article::all());

        $this->assertEquals($affected, $result['amount']);
        $this->assertEquals($affected, $result['affected']);

        $newAmount = 3;
        $affected = 0;

        $result = $model->setAmount($newAmount, $sync = false);
        $this->assertCount($newAmount, Article::all());

        $this->assertEquals($newAmount, $result['amount']);
        $this->assertEquals($affected, $result['affected']);

        $newAmount = 1;
        $affected = 2;
        $result = $model->setAmount($newAmount, $sync = false);
        $this->assertCount($newAmount, Article::all());

        $this->assertEquals($affected, $result['amount']);
        $this->assertEquals($affected, $result['affected']);
    }

    /**
     * @test
     */
    public function it_can_increment_its_amount()
    {
        $amount = 2;
        $model = factory(Article::class)->create();
        $model->refresh();

        $result = $model->incrementAmount($amount, $sync = false);

        $this->assertEquals($amount, $result['amount']);
        $this->assertEquals($amount, $result['affected']);

        $articles = Article::all();

        $this->assertCount(3, $articles);
        $this->assertEquals(1, $articles->first()->index);
        $this->assertEquals(3, $articles->last()->index);
    }

    /**
     * @test
     */
    public function it_can_decrement_its_amount()
    {
        $model = factory(Article::class)->create();
        $model->refresh();

        $this->assertEquals(Article::count(), $model->amount);

        $model = $model->copy();

        $this->assertEquals(Article::count(), $model->amount);

        $model = $model->copy();

        $this->assertEquals(Article::count(), $model->amount);

        $amount = 2;
        $result = $model->decrementAmount($amount, $sync = false);

        $this->assertEquals($amount, $result['amount']);
        $this->assertEquals($amount, $result['affected']);
        $this->assertEquals(1, Article::count());
        $this->assertEquals(1, Article::first()->index);

    }
}
