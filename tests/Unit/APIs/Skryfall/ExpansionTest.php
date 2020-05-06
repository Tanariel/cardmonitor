<?php

namespace Tests\Unit\APIs\Skryfall;

use App\APIs\Skryfall\Card;
use App\APIs\Skryfall\Expansion;
use Tests\TestCase;

class ExpansionTest extends TestCase
{
    const EXPANSION_CODE = 'iko';

    /**
     * @test
     */
    public function it_can_be_found_by_code()
    {
        $model = Expansion::findByCode(self::EXPANSION_CODE);
        $this->assertInstanceOf(Expansion::class, $model);
    }

    /**
     * @test
     */
    public function it_returns_null_if_not_found()
    {
        $model = Expansion::findByCode('invalid');
        $this->assertNull($model);
    }

    /**
     * @test
     */
    public function it_can_find_a_card_by_its_number()
    {
        $model = new Expansion();
        $model->code = self::EXPANSION_CODE;
        $this->assertInstanceOf(Card::class, $model->cards->firstByNumber(179));
        $this->assertNull($model->cards->firstByNumber(-1));
    }

    /**
     * @test
     */
    public function it_gets_all_its_cards()
    {
        $model = new Expansion();
        $model->code = self::EXPANSION_CODE;
        $this->assertCount(350, $model->cards);
    }
}
