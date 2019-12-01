<?php

namespace Tests\Feature\Controller\Images;

use App\Models\Images\Image;
use App\Models\Orders\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageableControllerTest extends TestCase
{
    protected $baseRouteName = 'order.images';
    protected $baseViewPath = 'image.imageable';
    protected $className = Image::class;

    protected function setUp() : void
    {
        parent::setUp();

        $this->order = factory(Order::class)->create([
            'user_id' => $this->user->id,
        ]);
    }

    /**
     * @test
     */
    public function guests_can_access_the_index_route()
    {
        $this->getIndexViewResponse(['order' => $this->order->id]);
    }

    /**
     * @test
     */
    public function guests_can_not_access_the_following_routes()
    {
        $id = factory($this->className)->create([
            'imageable_type' => Order::class,
            'imageable_id' => $this->order->id
        ])->id;

        $actions = [
            'store' => ['order' => $this->order->id],
        ];
        $this->guestsCanNotAccess($actions);
    }

    /**
     * @test
     */
    public function a_user_can_not_see_things_from_a_different_user()
    {
        $modelOfADifferentUser = factory(Order::class)->create();

        $this->signIn();

        $parameters = ['order' => $modelOfADifferentUser->id];

        $this->a_different_user_gets_a_403('store', 'post', $parameters);
    }

    /**
     * @test
     */
    public function a_user_can_see_the_index_view()
    {
        $this->getIndexViewResponse([
                'order' => $this->order->id,
            ])->assertViewIs($this->baseViewPath . '.index');
    }

    /**
     * @test
     */
    public function a_user_can_get_a_collection_of_models()
    {
        $this->withoutExceptionHandling();

        $models = factory($this->className, 3)->create([
            'user_id' => $this->user->id,
            'imageable_type' => Order::class,
            'imageable_id' => $this->order->id,
        ]);

        $this->getCollection([
            'order' => $this->order->id,
        ]);
    }

    /**
     * @test
     */
    public function a_user_can_create_a_file()
    {
        $this->withoutExceptionHandling();

        Storage::fake(config('app.storage_disk_userfiles'));

        $this->signIn();

        $file = UploadedFile::fake()->image('file.jpg');

        $response = $this->post(route($this->baseRouteName . '.store', [
            'order' => $this->order->id,
        ]), [
            'images' => [ $file ],
        ]);
        $response->assertStatus(Response::HTTP_FOUND)
            ->assertRedirect();

        $this->assertDatabaseHas('images', [
            'id' => 1,
            'imageable_type' => Order::class,
            'imageable_id' => $this->order->id,
            'user_id' => $this->user->id,
        ]);

        $image = Image::first();

        Storage::disk('public')->assertExists('images/' . $image->filename);

        $response = $this->json('POST', route($this->baseRouteName . '.store', [
            'order' => $this->order->id,
        ]), ['images' => [ $file ]]);
        $response->assertStatus(Response::HTTP_OK)
            ->assertJsonCount(1);
    }
}
