<?php

namespace Tests\Unit\Support\Users;

use App\Support\Users\FinTs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class FinTsTest extends TestCase
{
    /**
     * @test
     */
    public function it_gets_the_account()
    {
        $fints = new FinTs();
        $data = $fints->getAccount();

        dump($data);
    }

    /**
     * @test
     */
    public function it_gets_the_account_statements()
    {
        $fints = new FinTs();
        $data = $fints->getStatements(now()->sub(1, 'days'), now());

        dump($data);
    }

    /**
     * @test
     */
    public function it_can_get_transactions()
    {
        $fints = new FinTs();
        $data = $fints->getTransactions(now()->sub(1, 'days'), now());
        dump($data);
    }
}
