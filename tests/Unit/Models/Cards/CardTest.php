<?php

namespace Tests\Unit\Models\Cards;

use App\Models\Cards\Card;
use App\Models\Expansions\Expansion;
use App\Models\Localizations\Language;
use App\Models\Localizations\Localization;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\RelationshipAssertions;

class CardTest extends TestCase
{
    use RelationshipAssertions;

    /**
     * @test
     */
    public function it_has_many_localizations()
    {
        $model = factory(Card::class)->create();

        $this->assertMorphMany($model, Localization::class, 'localizations', 1);
    }

    /**
     * @test
     */
    public function a_default_localization_is_created_after_model_is_created()
    {
        $model = factory(Card::class)->create();

        $this->assertCount(1, $model->localizations);

        $this->assertDatabaseHas('localizations', [
            'localizationable_type' => Card::class,
            'localizationable_id' => $model->id,
            'language_id' => Language::DEFAULT_ID,
            'name' => $model->name,
        ]);
    }

    /**
     * @test
     */
    public function it_can_be_created_from_cardmarket()
    {
        $expansion = factory(Expansion::class)->create([
            'name' => 'Born of the Gods',
        ]);

        $cardmarketCard = [
            "idProduct" => 265882,
            "idMetaproduct" => 209344,
            "countReprints" => 1,
            "enName" => "Shrike Harpy",
            "locName" => "Würgerharpyie",
            "localization" => [
                0 => [
                    "name" => "Shrike Harpy",
                    "idLanguage" => "1",
                    "languageName" => "English",
                ],
                1 => [
                    "name" => "Harpie grièche",
                    "idLanguage" => "2",
                    "languageName" => "French",
                ],
                2 => [
                    "name" => "Würgerharpyie",
                    "idLanguage" => "3",
                    "languageName" => "German",
                ],
                3 => [
                    "name" => "Arpía impía",
                    "idLanguage" => "4",
                    "languageName" => "Spanish",
                ],
                4 => [
                    "name" => "Arpia Avèrla",
                    "idLanguage" => "5",
                    "languageName" => "Italian",
                ],
            ],
            "website" => "/en/Magic/Products/Singles/Born+of+the+Gods/Shrike-Harpy",
            "image" => "./img/items/1/BNG/265882.jpg",
            "gameName" => "Magic the Gathering",
            "categoryName" => "Magic Single",
            "idGame" => "1",
            "number" => "83",
            "rarity" => "Uncommon",
            "expansion" => [
              "idExpansion" => 1469,
              "enName" => "Born of the Gods",
              "expansionIcon" => 246,
            ],
            "priceGuide" => [
                "SELL" => 0.03,
                "LOW" => 0.01,
                "LOWEX" => 0.01,
                "LOWFOIL" => 0.03,
                "AVG" => 0.13,
                "TREND" => 0.05,
            ],
            "countArticles" => 3951,
            "countFoils" => 164,
            "links" => [
                0 => [
                    "rel" => "self",
                    "href" => "/products/265882",
                    "method" => "GET",
                ],
                1 => [
                    "rel" => "articles",
                    "href" => "/articles/265882",
                    "method" => "GET",
                ],
            ],
        ];

        $card = Card::createFromCardmarket($cardmarketCard, $expansion->id);
        $this->assertEquals(265882, $card->cardmarket_product_id);
        $this->assertEquals('Shrike Harpy', $card->name);
        $this->assertEquals('./img/items/1/BNG/265882.jpg', $card->image);
        $this->assertEquals('/en/Magic/Products/Singles/Born+of+the+Gods/Shrike-Harpy', $card->website);
        $this->assertEquals(1, $card->reprints_count);
        $this->assertEquals('Uncommon', $card->rarity);
        $this->assertEquals('83', $card->number);
        $this->assertEquals(1, $card->game_id);
        $this->assertCount(5, $card->localizations);
    }
}
