<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;

interface TransactionRRepository
{
    public function highUserTransactionsForGivenTime(string $time, int $limit): Collection|array;
}
