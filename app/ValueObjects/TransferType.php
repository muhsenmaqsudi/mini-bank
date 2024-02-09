<?php

namespace App\ValueObjects;

use App\Concerns\HasEnumValues;

enum TransferType: string
{
    use HasEnumValues;

    case DEPOSIT = 'deposit';
    case WITHDRAW = 'withdraw';
}
