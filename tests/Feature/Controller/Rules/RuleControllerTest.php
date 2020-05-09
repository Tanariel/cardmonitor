<?php

namespace Tests\Feature\Controller\Rules;

use App\Models\Rules\Rule;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Arr;
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
    public function a_user_can_not_see_things_from_a_different_user()
    {
        $modelOfADifferentUser = factory($this->className)->create();

        $this->a_user_can_not_see_models_from_a_different_user(['rule' => $modelOfADifferentUser->id]);
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

        $this->getShowViewResponse(['rule' => $model->id]);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_edit_view()
    {
        $model = $this->createModel();

        $this->getEditViewResponse(['rule' => $model->id]);
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
            'base_price' => 'trend',
            'description' => null,
            'expansion_id' => null,
            'is_altered' => false,
            'is_foil' => false,
            'is_playset' => false,
            'is_signed' => false,
            'min_price_common_formatted' => '1,23',
            'min_price_land_formatted' => '1,23',
            'min_price_masterpiece_formatted' => '1,23',
            'min_price_mythic_formatted' => '1,23',
            'min_price_rare_formatted' => '1,23',
            'min_price_special_formatted' => '1,23',
            'min_price_time_shifted_formatted' => '1,23',
            'min_price_tip_card_formatted' => '1,23',
            'min_price_token_formatted' => '1,23',
            'min_price_uncommon_formatted' => '1,23',
            'multiplier_formatted' => '1,23',
            'name' => 'Updated Model',
            'price_above_formatted' => '1,23',
            'price_below_formatted' => '1,23',
            'rarity' => null,
        ];

        $response = $this->put(route($this->baseRouteName . '.update', ['rule' => $model->id]), $data)
            ->assertStatus(Response::HTTP_FOUND)
            ->assertSessionHasNoErrors();

        Arr::forget($data, [
            'min_price_common_formatted',
            'min_price_land_formatted',
            'min_price_masterpiece_formatted',
            'min_price_mythic_formatted',
            'min_price_rare_formatted',
            'min_price_special_formatted',
            'min_price_time_shifted_formatted',
            'min_price_tip_card_formatted',
            'min_price_token_formatted',
            'min_price_uncommon_formatted',
            'multiplier_formatted',
            'price_above_formatted',
            'price_below_formatted',
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
        $this->withoutExceptionHandling();

        $model = $this->createModel();

        $this->deleteModel($model, ['rule' => $model->id])
            ->assertRedirect();
    }
}
