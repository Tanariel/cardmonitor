<?php

namespace Tests\Feature\Controller\Rules;

use App\Models\Rules\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Tests\TestCase;

class RuleControllerTest extends TestCase
{
    protected $baseRouteName = 'rule';
    protected $baseViewPath = 'rule';
    protected $className = Rule::class;

    /**
     * @test
     */
    public function guests_can_not_access_the_following_routes()
    {
        $id = factory($this->className)->create()->id;

        $actions = [
            'index' => [],
            'create' => [],
            'store' => [],
            'show' => ['rule' => $id],
            'edit' => ['rule' => $id],
            'update' => ['rule' => $id],
            'destroy' => ['rule' => $id],
        ];
        $this->guestsCanNotAccess($actions);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_index_view()
    {
        $this->getIndexViewResponse()
            ->assertViewIs($this->baseViewPath . '.index');
    }

    /**
     * @test
     */
    public function a_user_can_get_a_collection_of_models()
    {
        $models = factory($this->className, 3)->create([
            'user_id' => $this->user->id,
        ]);

        $this->getPaginatedCollection();
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
        ] + $data);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_show_view()
    {
        $this->withoutExceptionHandling();

        $model = $this->createModel();

        $this->getShowViewResponse(['rule' => $model->id])
            ->assertViewIs($this->baseViewPath . '.show')
            ->assertViewHas('model');
    }

    /**
     * @test
     */
    public function a_user_can_see_the_edit_view()
    {
        $model = $this->createModel();

        $this->getEditViewResponse(['rule' => $model->id])
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

        $data = [
            'name' => 'Updated Model',
        ];

        $response = $this->put(route($this->baseRouteName . '.update', ['rule' => $model->id]), $data)
            ->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas($model->getTable(), [
            'id' => $model->id,
        ] + $data);
    }

    /**
     * @test
     */
    public function a_user_can_delete_a_model()
    {
        $this->withoutExceptionHandling();

        $model = $this->createModel();

        $this->deleteModel($model, ['rule' => $model->id])
            ->assertRedirect();
    }
}
