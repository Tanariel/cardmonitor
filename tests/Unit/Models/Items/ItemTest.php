<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\Card;
use App\Models\Items\Item;
use App\Models\Items\Mailing;
use App\Models\Items\Quantity;
use App\Models\Items\Transactions\Purchase;
use App\Models\Items\Transactions\Transaction;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\RelationshipAssertions;

class ItemTest extends TestCase
{
    use RelationshipAssertions;

    /**
     * @test
     */
    public function it_creates_setup_models()
    {
        $user = factory(User::class)->create();

        $rarities = factory(\App\Models\Cards\Card::class, 5)->create()->pluck('rarity')->toArray();
        $rarities = array_unique($rarities);
        $rarities_count = count($rarities);

        $customs_count = 4;

        $items_count = $rarities_count + $customs_count;

        $this->assertCount($customs_count, $user->items);

        Item::setup($user);

        $this->assertCount($items_count, $user->fresh()->items);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'type' => Card::class,
            'name' => $rarities[0],
            'unit_cost' => Card::DEFAULT_PRICE,
        ]);
    }

    /**
     * @test
     */
    public function it_can_create_quantites()
    {
        $model = factory(Item::class)->create();

        $effective_from = new Carbon('1970-01-01 00:00:00', 'UTC');
        $quantities = [
            1 => [
                'start' => 1,
                'end' => 5,
            ],
            2 => [
                'start' => 6,
                'end' => 10,
            ],
            3 => [
                'start' => 11,
                'end' => null,
            ],
        ];

        $model->addQuantities($quantities, $effective_from);

        $this->assertCount(count($quantities), Quantity::where('item_id', $model->id)->get());

        foreach ($quantities as $quantity => $value) {
            $this->assertDatabaseHas('quantities', [
                'item_id' => $model->id,
                'effective_from' => $effective_from,
                'start' => $value['start'],
                'end' => $value['end'],
                'quantity' => $quantity,
            ]);
        }
    }

    /**
     * @test
     */
    public function it_has_many_transactions()
    {
        $model = factory(Item::class)->create();
        $related = factory(Transaction::class)->create([
            'user_id' => $model->user_id,
            'item_id' => $model->id,
        ]);

        $this->assertHasMany($model, $related, 'transactions');
    }

    /**
     * @test
     */
    public function it_has_many_quantities()
    {
        $model = factory(Item::class)->create();
        $related = factory(Quantity::class)->create([
            'user_id' => $model->user_id,
            'item_id' => $model->id,
        ]);

        $this->assertHasMany($model, $related, 'quantities');
    }
}
