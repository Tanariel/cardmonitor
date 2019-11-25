<?php

namespace App\Support\Users;

use Carbon\Carbon;

class FinTs
{
    protected $fints;

    public function __construct()
    {
        $this->fints = new \Fhp\FinTs(
            config('app.fints.bank_url'),
            443,
            config('app.fints.bank_code'),
            config('app.fints.username'),
            config('app.fints.pin'),
            null,
            config('app.fints.registration_no'),
            '1.0'
        );

        $this->fints->setTANMechanism(config('app.fints.tan_mechanism'));
    }

    public function getAccount() : \Fhp\Model\SEPAAccount
    {
        $accounts = $this->fints->getSEPAAccounts();
        return $accounts[0];
    }

    public function getStatements(Carbon $from, Carbon $to)
    {
        return $this->fints->getStatementOfAccount($this->getAccount(), $from, $to);
    }

    public function getTransactions(Carbon $from, Carbon $to) : array
    {
        $transactions = [];
        $statements = $this->getStatements($from, $to);
        foreach ($statements->getStatements() as $statement)
        {
            $date = new Carbon($statement->getDate()->format('Y-m-d'));
            foreach ($statement->getTransactions() as $SEPATransaction)
            {
                $transactions[] = $SEPATransaction;
            }
        }

        return $transactions;
    }

    public function import()
    {
        $statements = $this->getStatements($from, $to);
        foreach ($statements->getStatements() as $statement)
        {
            $date = new Carbon($statement->getDate()->format('Y-m-d'));
            foreach ($statement->getTransactions() as $SEPATransaction)
            {
                // Import
            }
        }
    }
}

?>