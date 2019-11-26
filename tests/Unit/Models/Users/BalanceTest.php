<?php

namespace Tests\Unit\Models\Users;

use App\Models\Users\Balance;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class BalanceTest extends TestCase
{
    /**
     * @test
     */
    public function it_can_be_created_from_a_sepa_transaction()
    {
        $sepaTransaction = new \Fhp\Model\StatementOfAccount\Transaction();
        $model = Balance::createFromTransaction($sepaTransaction);
    }

    /**
     * @test
     */
    public function it_guesses_its_user()
    {
        $model = factory(Balance::class)->create([
            'user_id' => null,
            'booking_text' => 'Cardmonitor ' . $this->user->id,
        ]);

        $model->guessUser();

        $this->assertEquals($this->user->id, $model->user_id);

        $model = factory(Balance::class)->create([
            'user_id' => null,
            'booking_text' => 'Cardmonitor 0',
        ]);

        $model->guessUser();

        $this->assertEquals(null, $model->user_id);
    }
}
