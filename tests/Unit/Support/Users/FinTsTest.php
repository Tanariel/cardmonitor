<?php

namespace Tests\Unit\Support\Users;

use App\Support\Users\FinTs;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use nemiah\phpSepaXml\SEPACreditor;
use nemiah\phpSepaXml\SEPADebitor;
use nemiah\phpSepaXml\SEPATransfer;

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

    /**
     * @test
     */
    public function it_can_transfer_money()
    {
        $dt = new \DateTime();
        $dt->add(new \DateInterval("P1D"));
        $sepaDD = new SEPATransfer([
            'messageID' => time(),
            'paymentID' => time()
        ]);
        $sepaDD->setDebitor(new SEPADebitor([ //this is you
            'name' => 'Daniel Sundermeier',
            'iban' => 'DE25120300001059268977',
            'bic' => 'BYLADEM1001'#,
            #'identifier' => 'DE98ZZZ09999999999'
        ]));
        $sepaDD->addCreditor(new SEPACreditor([ //this is who you want to send money to
            #'paymentID' => '20170403652',
            'info' => 'Cardmonitor',
            'name' => 'Daniel Sundermeier',
            'iban' => 'DE16701204008492435006',
            'bic' => 'DABBDEMMXXX',
            'amount' => 0.01,
            'currency' => 'EUR',
            'reqestedExecutionDate' => $dt
        ]));

        $fints = new FinTs();
        $account = $fints->getAccount();
        dump($account);

        $transfer = $fints->transfer($sepaDD);
        dump($transfer);

        // TAN benötigt
    }
}
