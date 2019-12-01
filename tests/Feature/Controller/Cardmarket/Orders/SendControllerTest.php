<?php

namespace Tests\Feature\Controller\Cardmarket\Orders;

use App\Models\Orders\Order;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SendControllerTest extends TestCase
{
    protected $baseRouteName = 'order.send';
    protected $baseViewPath = 'order';
    protected $className = Order::class;

    /**
     * @test
     */
    public function a_user_can_not_see_things_from_a_different_user()
    {
        $modelOfADifferentUser = factory($this->className)->create();

        $this->signIn();

        $parameters = ['order' => $modelOfADifferentUser->id];

        $this->a_different_user_gets_a_403('store', 'post', $parameters);
    }
}
