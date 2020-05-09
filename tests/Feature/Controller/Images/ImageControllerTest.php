<?php

namespace Tests\Feature\Controller\Images;

use App\Models\Images\Image;
use App\Models\Orders\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageControllerTest extends TestCase
{
    protected $baseRouteName = 'image';
    protected $baseViewPath = 'image';
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
    public function guests_can_not_access_the_following_routes()
    {
        $id = factory($this->className)->create()->id;
        $order = factory(Order::class)->create();

        $actions = [
            'index' => [],
            'destroy' => ['image' => $id],
        ];
        $this->guestsCanNotAccess($actions);
    }

    /**
     * @test
     */
    public function a_user_can_not_see_things_from_a_different_user()
    {
        $modelOfADifferentUser = factory($this->className)->create();

        $this->signIn();

        $parameters = ['image' => $modelOfADifferentUser->id];

        $this->a_different_user_gets_a_403('destroy', 'delete', $parameters);
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
    public function a_user_can_delete_an_item_if_it_is_deletable()
    {
        $this->withoutExceptionHandling();

        $this->signIn($this->user);

        $model = $this->createImage();

        $this->deleteModel($model, ['image' => $model->id])
            ->assertRedirect();

        Storage::disk('public')->assertMissing('images/' . $model->filename);
    }

    protected function createImage() : Image
    {
        Storage::fake('public');
        $file = UploadedFile::fake()->image('images/file.jpg');
        $image = Image::createFromUploadedFile($file, $this->order);

        Storage::disk('public')->assertExists('images/' . $image->filename);
        $this->assertDatabaseHas('images', [
            'id' => $image->id,
        ]);

        return $image;
    }
}
