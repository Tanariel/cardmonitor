<?php

namespace Tests;

use App\Models\Items\Item;
use App\Models\Localizations\Language;
use App\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;
use Illuminate\Http\Response;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication, RefreshDatabase;

    protected $user;

    protected function setUp() : void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
        $languages = [
            1 => 'English',
            2 => 'French',
            3 => 'German',
            4 => 'Spanish',
            5 => 'Italian',
        ];
        foreach ($languages as $id => $name) {
            $language = Language::create([
                'id' => $id,
                'name' => $name,
            ]);
            if ($id == Language::DEFAULT_ID) {
                $this->defaultLanguage = $language;
            }
        }

        Item::setup($this->user);
    }

    public function signIn(User $user = null)
    {
        if (is_null($user))
        {
            $user = $this->user;
        }

        if (! $this->isAuthenticated())
        {
            $this->actingAs($user);
        }

        return $user;
    }

    public function guestsCanNotAccess(array $actions) : void
    {
        $verbs = [
            'index' => 'get',
            'create' => 'get',
            'store' => 'post',
            'show' => 'get',
            'edit' => 'get',
            'update' => 'put',
            'destroy' => 'delete',
        ];

        foreach ($actions as $action => $parameters) {
            $this->assertAuthenticationRequired($action, $verbs[$action], $parameters);
        }
    }

    protected function assertAuthenticationRequired(string $action, string $method = 'get', array $parameters = []) : void
    {
        $this->$method(route($this->baseRouteName . '.' . $action, $parameters))
            ->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect(basename(route('login')));

        $method .= 'Json';
        $this->$method(route($this->baseRouteName . '.' . $action, $parameters))
            ->assertStatus(Response::HTTP_UNAUTHORIZED);
    }

    public function getIndexViewResponse(array $parameters = []) : TestResponse
    {
        return $this->getViewResponse('index', $parameters);
    }

    public function getCreateViewResponse(array $parameters = []) : TestResponse
    {
        return $this->getViewResponse('create', $parameters);
    }

    public function getShowViewResponse(array $parameters = []) : TestResponse
    {
        return $this->getViewResponse('show', $parameters);
    }

    public function getEditViewResponse(array $parameters = []) : TestResponse
    {
        return $this->getViewResponse('edit', $parameters);
    }

    protected function getViewResponse(string $action, array $parameters = []) : TestResponse
    {
        $this->signIn();

        $response = $this->get(route($this->baseRouteName . '.' . $action, $parameters));
        $response->assertStatus(Response::HTTP_OK);

        return $response;
    }

    public function getCollection(array $parameters = [], int $assertJsonCount = 3) : TestResponse
    {
        $this->signIn();

        $response = $this->getJson(route($this->baseRouteName . '.index', $parameters))
            ->assertStatus(Response::HTTP_OK)
            ->assertJsonCount($assertJsonCount);

        return $response;
    }

    public function getPaginatedCollection(array $parameters = [], int $assertJsonCount = 3) : TestResponse
    {
        $this->signIn();

        $response = $this->json('get', route($this->baseRouteName . '.index', $parameters), []);

        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonStructure([
                'current_page',
                'data',
                'total',
            ])
            ->assertJsonCount($assertJsonCount, 'data');

        return $response;
    }

    public function deleteModel(Model $model, array $parameters) : TestResponse
    {
        $this->signIn();

        $table = $model->getTable();

        $this->assertDatabaseHas($table, [
            'id' => $model->id
        ]);

        $this->assertTrue($model->isDeletable());
        $response = $this->delete(route($this->baseRouteName . '.destroy', $parameters))
            ->assertStatus(Response::HTTP_FOUND);

        $this->assertDatabaseMissing($table, [
            'id' => $model->id
        ]);

        return $response;
    }

    protected function createModel() : Model
    {
        return factory($this->className)->create([
            'user_id' => $this->user->id,
        ])->fresh();
    }
}
