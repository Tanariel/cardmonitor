<?php

namespace Tests\Unit\APIs\Skryfall;

use App\APIs\Skryfall\Card;
use App\APIs\Skryfall\CardCollection;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class CardTest extends TestCase
{
    /**
     * @test
     */
    public function it_finds_a_card_by_code_and_number()
    {
        $model = Card::findByCodeAndNumber('mmq', 1);
        $this->assertInstanceOf(Card::class, $model);
    }

    /**
     * @test
     */
    public function it_returns_null_if_not_found()
    {
        $model = Card::findByCodeAndNumber('mmq', 99999);
        $this->assertNull($model);
    }

    /**
     * @test
     */
    public function it_gets_colors_string_attribute()
    {
        $model = new Card();
        $model->colors = ['b', 'w'];
        $this->assertEquals('b, w', $model->colors_string);
    }

    /**
     * @test
     */
    public function it_gets_color_identity_string_attribute()
    {
        $model = new Card();
        $model->color_identity = ['b', 'w'];
        $this->assertEquals('b, w', $model->color_identity_string);
    }

    /**
     * @test
     */
    public function it_has_a_release_date()
    {
        $model = new Card();
        $model->released_at = '2017-04-28';
        $this->assertInstanceOf(Carbon::class, $model->released_at);
    }

    /**
     * @test
     */
    public function it_gets_all_cards_from_a_set()
    {
        $collection = Card::fromSet('mmq');
        $this->assertInstanceOf(CardCollection::class, $collection);
        $this->assertCount(350, $collection);
    }
}
