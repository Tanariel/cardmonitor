<?php

namespace Tests\Feature\Controller\Articles\Stock;

use App\Models\Articles\Article;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StockControllerTest extends TestCase
{
    protected $baseRouteName = 'article.stock';
    protected $baseViewPath = 'article.stock';
    protected $className = Article::class;

    /**
     * @test
     */
    public function guests_can_not_access_the_following_routes()
    {
        $id = factory($this->className)->create()->id;

        $actions = [
            'index' => [],
        ];
        $this->guestsCanNotAccess($actions);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_index_view()
    {
        $this->withoutExceptionHandling();
        $this->getIndexViewResponse();
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
    public function a_user_can_update_a_model()
    {
        $this->withoutExceptionHandling();

        $model = $this->createModel();

        $this->signIn($this->user);

        $newAmount = 3;
        $data = [
            'amount' => $newAmount,
            'sync' => false,
        ];

        $response = $this->put(route($this->baseRouteName . '.update', ['article' => $model->id]), $data)
            ->assertStatus(Response::HTTP_OK)
            ->assertSessionHasNoErrors();

        $this->assertCount($newAmount, Article::all());
    }
}
