<?php

namespace Tests\Feature\Controller\Items\Transactions;

use App\Models\Items\Item;
use App\Models\Items\Transactions\Purchase;
use App\Models\Items\Transactions\Transaction;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    protected $baseRouteName = 'transaction';
    protected $baseViewPath = 'item.transaction';
    protected $className = Transaction::class;

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
            'show' => ['transaction' => $id],
            'edit' => ['transaction' => $id],
            'update' => ['transaction' => $id],
            'destroy' => ['transaction' => $id],
        ];
        $this->guestsCanNotAccess($actions);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_index_view()
    {
        $model = $this->createModel();

        $this->getIndexViewResponse(['item' => $model->item_id]);
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
            'type' => Purchase::class,
            'quantity_formatted' => '1',
            'unit_cost_formatted' => '1,23',
            'at_formatted' => $at->format('d.m.Y H:i'),
        ];

        $this->post(route($this->baseRouteName . '.store', ['item' => $item->id]), $data)
            ->assertStatus(Response::HTTP_CREATED);

        $data['quantity'] = 1.000000;
        $data['unit_cost'] = 1.230000;
        $data['at'] = $at->format('Y-m-d H:i:s');

        Arr::forget($data, [
            'at_formatted',
            'quantity_formatted',
            'unit_cost_formatted',
        ]);

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
        $this->withoutExceptionHandling();

        $model = $this->createModel();

        $this->getShowViewResponse(['transaction' => $model->id]);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_edit_view()
    {
        $model = $this->createModel();

        $this->getEditViewResponse(['transaction' => $model->id]);
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
            'quantity_formatted' => '1',
            'unit_cost_formatted' => '1,23',
            'at_formatted' => $at->format('d.m.Y H:i'),
        ];

        $response = $this->put(route($this->baseRouteName . '.update', ['transaction' => $model->id]), $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertSessionHasNoErrors();

        $data['quantity'] = 1.000000;
        $data['unit_cost'] = 1.230000;
        $data['at'] = $at->format('Y-m-d H:i:s');

        Arr::forget($data, [
            'at_formatted',
            'quantity_formatted',
            'unit_cost_formatted',
        ]);

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

        $this->deleteModel($model, ['transaction' => $model->id])
            ->assertRedirect();
    }

    protected function createModel(array $attributes = []) : Model
    {
        return factory($this->className)->create([
            'user_id' => $this->user->id,
        ])->fresh();
    }
}
