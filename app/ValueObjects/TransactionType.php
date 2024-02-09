<?php

namespace App\ValueObjects;

use App\Concerns\HasEnumValues;

enum TransactionType: string
{
    use HasEnumValues;

    case TRANSFER = 'transfer';
    case REFUND = 'refund';
}
