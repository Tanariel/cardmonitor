<?php

namespace Tests\Feature\Controller\Items;

use App\Models\Items\Card;
use App\Models\Items\Custom;
use App\Models\Items\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class ItemControllerTest extends TestCase
{
    protected $baseRouteName = 'item';
    protected $baseViewPath = 'item';
    protected $className = Item::class;

    /**
     * @test
     */
    public function guests_can_not_access_the_following_routes()
    {
        $id = factory($this->className)->create()->id;

        $actions = [
            'index' => [],
            'store' => [],
            'show' => ['item' => $id],
            'edit' => ['item' => $id],
            'update' => ['item' => $id],
            'destroy' => ['item' => $id],
        ];
        $this->guestsCanNotAccess($actions);
    }

    /**
     * @test
     */
    public function a_user_can_not_see_things_from_a_different_user()
    {
        $modelOfADifferentUser = factory($this->className)->create();

        $this->a_user_can_not_see_models_from_a_different_user(['item' => $modelOfADifferentUser->id]);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_index_view()
    {
        $this->getIndexViewResponse();
    }

    /**
     * @test
     */
    public function a_user_can_get_a_collection_of_models()
    {
        $this->getCollection([], 4);
    }

    /**
     * @test
     */
    public function a_user_can_create_a_model()
    {
        $this->withoutExceptionHandling();

        $this->signIn();

        $data = [
            'name' => 'New Model',
        ];

        $this->post(route($this->baseRouteName . '.store'), $data)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas((new $this->className)->getTable(), [
            'user_id' => $this->user->id,
            'type' => Custom::class,
        ] + $data);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_show_view()
    {
        $this->withoutExceptionHandling();

        $model = $this->createModel();

        $this->getShowViewResponse(['item' => $model->id]);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_edit_view()
    {
        $model = $this->createModel();

        $this->getEditViewResponse(['item' => $model->id]);
    }

    /**
     * @test
     */
    public function a_user_can_update_a_model()
    {
        $this->withoutExceptionHandling();

        $model = $this->createModel();

        $this->signIn($this->user);

        $data = [
            'name' => 'Updated Model',
            'unit_cost_formatted' => '1,23',
        ];

        $response = $this->put(route($this->baseRouteName . '.update', ['item' => $model->id]), $data)
            ->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas($model->getTable(), [
            'id' => $model->id,
            'name' => 'Updated Model',
            'unit_cost' => 1.23,
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_delete_a_model()
    {
        $model = $this->createModel();

        $this->deleteModel($model, ['item' => $model->id])
            ->assertRedirect();
    }

    /**
     * @test
     */
    public function a_user_can_not_delete_special_models()
    {
        $table = 'items';
        $model = factory(Item::class)->create([
            'type' => Card::class,
            'user_id' => $this->user->id,
        ]);

        $this->signIn();

        $this->assertFalse($model->isDeletable());
        $response = $this->delete(route($this->baseRouteName . '.destroy', ['item' => $model->id]))
            ->assertStatus(Response::HTTP_FOUND);

        $this->assertDatabaseHas($table, [
            'id' => $model->id
        ]);
    }
}
