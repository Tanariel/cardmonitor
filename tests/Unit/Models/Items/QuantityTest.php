<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\Quantity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\AttributeAssertions;

class QuantityTest extends TestCase
{
    use AttributeAssertions;

    /**
     * @test
     */
    public function it_sets_effektive_from_from_formated_value()
    {
        $this->assertSetsFormattedDatetime(Quantity::class, 'effective_from');
    }

    /**
     * @test
     */
    public function it_sets_quantity_from_formated_value()
    {
        $this->assertSetsFormattedNumber(Quantity::class, 'quantity');
    }

    /**
     * @test
     */
    public function it_sets_start_from_formated_value()
    {
        $this->assertSetsFormattedNumber(Quantity::class, 'start');
    }

    /**
     * @test
     */
    public function it_sets_end_from_formated_value()
    {
        $this->assertSetsFormattedNullableNumber(Quantity::class, 'end');
    }
}
