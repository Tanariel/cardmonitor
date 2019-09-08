<?php

namespace Tests\Unit\Models\Items\Transactions;

use App\Models\Items\Item;
use App\Models\Items\Transactions\Purchase;
use App\Models\Items\Transactions\Transaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\AttributeAssertions;
use Tests\Traits\RelationshipAssertions;

class TransactionTest extends TestCase
{
    use AttributeAssertions, RelationshipAssertions;

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

    /**
     * @test
     */
    public function it_belongs_to_an_item()
    {
        $related = factory(Item::class)->create();
        $model = factory(Transaction::class)->create([
            'user_id' => $related->user_id,
        ]);

        $this->assertBelongsTo($model, $related, 'item');
    }

}
