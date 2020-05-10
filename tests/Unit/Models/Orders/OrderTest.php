<?php

namespace Tests\Unit\Models\Orders;

use App\Models\Articles\Article;
use App\Models\Images\Image;
use App\Models\Items\Card;
use App\Models\Items\Item;
use App\Models\Orders\Evaluation;
use App\Models\Orders\Order;
use App\Models\Users\CardmarketUser;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
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
    public function it_knows_its_routes()
    {
        $model = factory(Order::class)->create();
        $this->assertEquals(config('app.url') . '/order/' . $model->id, $model->path);
        $this->assertEquals(config('app.url') . '/order/' . $model->id . '/edit', $model->editPath);
    }

    /**
     * @test
     */
    public function it_knows_if_it_can_have_images()
    {
        $buyer = factory(CardmarketUser::class)->create([
            'cardmarket_user_id' => 1,
        ]);

        $seller = factory(CardmarketUser::class)->create([
            'cardmarket_user_id' => 2,
        ]);

        $model = factory(Order::class)->create([
            'received_at' => null,
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);
        $this->assertTrue($model->canHaveImages());

        $model = factory(Order::class)->create([
            'received_at' => now(),
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);
        $this->assertTrue($model->canHaveImages());

        $model = factory(Order::class)->create([
            'received_at' => now()->subDays(Order::DAYS_TO_HAVE_IAMGES),
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);
        $this->assertTrue($model->canHaveImages());

        $model = factory(Order::class)->create([
            'received_at' => now()->subDays((Order::DAYS_TO_HAVE_IAMGES + 1)),
            'buyer_id' => $buyer->id,
            'seller_id' => $seller->id,
        ]);
        $this->assertFalse($model->canHaveImages());
    }

    /**
     * @test
     */
    public function it_has_one_evaluation()
    {
        $model = factory(Order::class)->create();
        $related = factory(Evaluation::class)->create([
            'order_id' => $model->id,
        ]);

        $this->assertHasOne($model, $related, 'evaluation');
    }

    /**
     * @test
     */
    public function it_belongs_to_many_articles()
    {
        $model = factory(Order::class)->create();
        $related = factory(Article::class)->create();

        $this->assertEquals(BelongsToMany::class, get_class($model->articles()));
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
    public function it_can_be_imported()
    {
        $this->markTestIncomplete('This test has not been implemented yet.');
    }

    /**
     * @test
     */
    public function it_finds_its_items()
    {
        $model = factory(Order::class)->create([
            'articles_count' => 25,
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
            'end' => 9999,
            'quantity' => 2,
            'start' => 51,
            'user_id' => $model->user_id,
        ]);

        $model->findItems();

        $this->assertCount(1, $model->fresh()->sales()->where('item_id', $item->id)->get());

        $this->assertEquals(1, $model->sales()->where('item_id', $item->id)->first()->quantity);

    }
}
