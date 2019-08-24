<?php

namespace Tests\Feature\Controller\Apis;

use App\Models\Apis\Api;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Tests\TestCase;

class ApiControllerTest extends TestCase
{
    protected $baseRouteName = 'api';
    protected $baseViewPath = 'api';
    protected $className = Api::class;

    /**
     * @test
     */
    public function guests_can_not_access_the_following_routes()
    {
        $id = factory($this->className)->create()->id;

        $actions = [
            'index' => [],
            'store' => [],
            'show' => ['api' => $id],
            'edit' => ['api' => $id],
            'update' => ['api' => $id],
            'destroy' => ['api' => $id],
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
    public function a_user_can_create_a_model()
    {
        $this->signIn();

        $data = [
            'app_token' => Str::random(),
            'app_secret' => Str::random(),
            'access_token' => Str::random(),
            'access_token_secret' => Str::random(),
        ];

        $this->post(route($this->baseRouteName . '.store'), $data)
            ->assertStatus(Response::HTTP_CREATED);

        $this->assertDatabaseHas((new $this->className)->getTable(), [
            'user_id' => $this->user->id,
            'accessdata' => json_encode($data),
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_show_view()
    {
        $this->withoutExceptionHandling();

        $model = $this->createModel();

        $this->getShowViewResponse(['api' => $model->id]);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_edit_view()
    {
        $model = $this->createModel();

        $this->getEditViewResponse(['api' => $model->id])
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
            'app_token' => Str::random(),
            'app_secret' => Str::random(),
            'access_token' => Str::random(),
            'access_token_secret' => Str::random(),
        ];

        $response = $this->put(route($this->baseRouteName . '.update', ['api' => $model->id]), $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertSessionHasNoErrors();

        $this->assertDatabaseHas($model->getTable(), [
            'id' => $model->id,
            'accessdata' => json_encode($data),
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_delete_a_model()
    {
        $model = $this->createModel();

        $this->deleteModel($model, ['api' => $model->id])
            ->assertRedirect();
    }

    protected function createModel() : Model
    {
        return factory($this->className)->create([
            'user_id' => $this->user->id,
        ]);
    }
}
