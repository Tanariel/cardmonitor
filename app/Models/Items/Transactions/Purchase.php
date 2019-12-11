<?php

namespace App\Models\Items\Transactions;

use App\Models\Items\Transactions\Transaction;
use Illuminate\Database\Eloquent\Model;
use Parental\HasParent;

class Purchase extends Transaction
{
    use HasParent;
}
