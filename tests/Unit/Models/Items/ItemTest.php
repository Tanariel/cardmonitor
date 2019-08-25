<?php

namespace Tests\Unit\Models\Items;

use App\Models\Items\Card;
use App\Models\Items\Item;
use App\Models\Items\Mailing;
use App\Models\Items\Quantity;
use App\Models\Items\Transactions\Purchase;
use App\Models\Items\Transactions\Transaction;
use App\User;
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

        $this->assertCount(0, $user->items);

        Item::setup($user);

        $this->assertCount(2, $user->fresh()->items);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'type' => Card::class,
            'name' => 'Karte',
        ]);

        $this->assertDatabaseHas('items', [
            'user_id' => $user->id,
            'type' => Mailing::class,
            'name' => 'Versandkosten',
        ]);
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
