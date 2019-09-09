<?php

namespace Tests\Unit\Models\Orders;

use App\Models\Articles\Article;
use App\Models\Images\Image;
use App\Models\Items\Item;
use App\Models\Orders\Order;
use App\Models\Users\CardmarketUser;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Tests\Traits\RelationshipAssertions;

class OrderTest extends TestCase
{
    use RelationshipAssertions;

    /**
     * @test
     */
    public function it_has_many_articles()
    {
        $model = factory(Order::class)->create();
        $related = factory(Article::class)->create([
            'order_id' => $model->id,
        ]);

        $this->assertHasMany($model, $related, 'articles');
    }

    /**
     * @test
     */
    public function it_belongs_to_a_buyer()
    {
        $cardmarketUser = factory(CardmarketUser::class)->create();

        $model = factory(Order::class)->create([
            'buyer_id' => $cardmarketUser->id
        ]);

        $this->assertBelongsTo($model, $cardmarketUser, 'buyer');
    }

    /**
     * @test
     */
    public function it_belongs_to_a_seller()
    {
        $cardmarketUser = factory(CardmarketUser::class)->create();

        $model = factory(Order::class)->create([
            'seller_id' => $cardmarketUser->id
        ]);

        $this->assertBelongsTo($model, $cardmarketUser, 'seller');
    }

    /**
     * @test
     */
    public function it_has_many_images()
    {
        $model = factory(Order::class)->create();

        $this->assertMorphMany($model, Image::class, 'images');
    }

    /**
     * @test
     */
    public function it_finds_its_items()
    {
        $model = factory(Order::class)->create([
            'cards_count' => 25,
        ]);

        $item = factory(Item::class)->create([
            'user_id' => $model->user_id,
        ]);
        $item->quantities()->create([
            'effective_from' => '1970-00-00 02:00:00',
            'end' => 50,
            'quantity' => 1,
            'start' => 1,
            'user_id' => $model->user_id,
        ]);
        $item->quantities()->create([
            'effective_from' => '1970-00-00 02:00:00',
            'end' => null,
            'quantity' => 2,
            'start' => 51,
            'user_id' => $model->user_id,
        ]);
        $model->findItems();

        $this->assertCount(1, $model->fresh()->sales);

        $this->assertEquals(1, $model->sales->first()->quantity);

    }
}