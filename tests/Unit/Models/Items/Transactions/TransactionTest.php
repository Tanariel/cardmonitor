<?php

namespace Tests\Unit\Models\Items\Transactions;

use App\Models\Items\Transactions\Purchase;
use App\Models\Items\Transactions\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\AttributeAssertions;

class TransactionTest extends TestCase
{
    use AttributeAssertions;

    /**
     * @test
     */
    public function it_sets_at_from_formated_value()
    {
        $this->assertSetsFormattedDatetime(Transaction::class, 'at');
    }

    /**
     * @test
     */
    public function it_sets_quantity_from_formated_value()
    {
        $this->assertSetsFormattedNumber(Transaction::class, 'quantity');
    }

    /**
     * @test
     */
    public function it_sets_unit_cost_from_formated_value()
    {
        $this->assertSetsFormattedNumber(Transaction::class, 'unit_cost');
    }

}
