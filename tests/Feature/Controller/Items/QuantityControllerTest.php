<?php

namespace Tests\Feature\Controller\Items;

use App\Models\Items\Item;
use App\Models\Items\Quantity;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class QuantityControllerTest extends TestCase
{
    protected $baseRouteName = 'quantity';
    protected $baseViewPath = 'item.quantity';
    protected $className = Quantity::class;

    /**
     * @test
     */
    public function guests_can_not_access_the_following_routes()
    {
        $model = $this->createModel();
        $id = $model->id;
        $itemId = $model->item_id;

        $actions = [
            'index' => ['item' => $itemId],
            'store' => ['item' => $itemId],
            'show' => ['quantity' => $id],
            'edit' => ['quantity' => $id],
            'update' => ['quantity' => $id],
            'destroy' => ['quantity' => $id],
        ];
        $this->guestsCanNotAccess($actions);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_index_view()
    {
        $model = $this->createModel();

        $this->getIndexViewResponse(['item' => $model->item_id])
            ->assertViewIs($this->baseViewPath . '.index');
    }

    /**
     * @test
     */
    public function a_user_can_get_a_paginated_collection_of_models()
    {
        $this->withoutExceptionHandling();

        $item = factory(Item::class)->create([
            'user_id' => $this->user->id,
        ]);

        $models = factory($this->className, 3)->create([
            'user_id' => $this->user->id,
            'item_id' => $item->id,
        ]);

        $this->getPaginatedCollection(['item' => $item->id]);
    }

    /**
     * @test
     */
    public function a_user_can_create_a_model()
    {
        $this->withoutExceptionHandling();

        $item = factory(Item::class)->create([
            'user_id' => $this->user->id,
        ]);

        $this->signIn();

        $at = today()->setTime(12, 34, 0);
        $data = [
            'effective_from_formatted' => $at->format('d.m.Y H:i'),
            'end_formatted' => null,
            'quantity_formatted' => '1',
            'start_formatted' => '1,23',
        ];

        $this->post(route($this->baseRouteName . '.store', ['item' => $item->id]), $data)
            ->assertStatus(Response::HTTP_CREATED);

        $data = [
            'effective_from' => $at->format('Y-m-d H:i:s'),
            'end' => null,
            'quantity' => 1,
            'start' => 1.23,
        ];

        $this->assertDatabaseHas((new $this->className)->getTable(), [
            'user_id' => $this->user->id,
            'item_id' => $item->id,
        ] + $data);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_show_view()
    {
        $model = $this->createModel();

        $this->getShowViewResponse(['quantity' => $model->id])
            ->assertViewIs($this->baseViewPath . '.show')
            ->assertViewHas('model');
    }

    /**
     * @test
     */
    public function a_user_can_see_the_edit_view()
    {
        $model = $this->createModel();

        $this->getEditViewResponse(['quantity' => $model->id])
            ->assertViewIs($this->baseViewPath . '.edit')
            ->assertViewHas('model');
    }

    /**
     * @test
     */
    public function a_user_can_update_a_model()
    {
        $this->withoutExceptionHandling();

        $model = $this->createModel();

        $this->signIn($this->user);

        $at = today()->setTime(12, 34, 0);
        $data = [
            'effective_from_formatted' => $at->format('d.m.Y H:i'),
            'end_formatted' => null,
            'quantity_formatted' => '1',
            'start_formatted' => '1,23',
        ];

        $response = $this->put(route($this->baseRouteName . '.update', ['quantity' => $model->id]), $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertSessionHasNoErrors();

        $data = [
            'effective_from' => $at->format('Y-m-d H:i:s'),
            'end' => null,
            'quantity' => 1,
            'start' => 1.23,
        ];

        $this->assertDatabaseHas($model->getTable(), [
            'id' => $model->id,
        ] + $data);
    }

    /**
     * @test
     */
    public function a_user_can_delete_a_model()
    {
        $model = $this->createModel();

        $this->deleteModel($model, ['quantity' => $model->id])
            ->assertRedirect();
    }
}
