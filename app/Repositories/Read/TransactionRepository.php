<?php

namespace App\Repositories\Read;

use App\Models\Transaction;
use App\Repositories\Contracts\TransactionRRepository;
use Illuminate\Database\Eloquent\Collection;

class TransactionRepository implements TransactionRRepository
{
    public function highUserTransactionsForGivenTime(string $time, int $limit): Collection|array
    {
        return Transaction::query()
            ->selectRaw('accounts.user_id, COUNT(transactions.id) as txn_counts')
            ->join('cards', 'transactions.card_id', '=', 'cards.id')
            ->join('accounts', 'cards.account_id', '=', 'accounts.id')
            ->whereDate(
                'transactions.created_at',
                '>=',
                $time
            )
            ->groupBy('accounts.user_id')
            ->orderByDesc('txn_counts')
            ->limit(3)
            ->get();
    }
}
