<?php

namespace Tests\Unit\Models\Articles;

use App\Models\Articles\Article;
use App\Models\Cards\Card;
use App\Models\Localizations\Language;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
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
    public function it_calculates_its_provision()
    {
        $model = new Article();
        $model->unit_price = 1;
        $this->assertEquals(0.05, $model->provision);

        $model->unit_price = 0.02;
        $this->assertEquals(0.01, $model->provision);

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

        $this->assertArrayHasKey('idProduct', $cardmarketModel);
        $this->assertArrayHasKey('idLanguage', $cardmarketModel);
        $this->assertArrayHasKey('comments', $cardmarketModel);
        $this->assertArrayHasKey('count', $cardmarketModel);
        $this->assertArrayHasKey('price', $cardmarketModel);
        $this->assertArrayHasKey('condition', $cardmarketModel);
        $this->assertArrayHasKey('isFoil', $cardmarketModel);
        $this->assertArrayHasKey('isSigned', $cardmarketModel);
        $this->assertArrayHasKey('isPlayset', $cardmarketModel);
    }
}
