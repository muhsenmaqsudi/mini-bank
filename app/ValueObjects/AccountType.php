<?php

namespace App\ValueObjects;

use App\Concerns\HasEnumValues;

enum AccountType: string
{
    use HasEnumValues;

    case MASTER = 'master';
    case NORMAL = 'normal';
}
