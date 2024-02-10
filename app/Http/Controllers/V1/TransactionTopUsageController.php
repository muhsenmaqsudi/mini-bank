<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserCollection;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionTopUsageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): UserCollection
    {
        $txnUsage = Transaction::query()
            ->selectRaw('accounts.user_id, COUNT(transactions.id) as txn_counts')
            ->join('cards', 'transactions.card_id', '=', 'cards.id')
            ->join('accounts', 'cards.account_id', '=', 'accounts.id')
            ->whereDate(
                'transactions.created_at',
                '>=',
                now()->subMinutes(15)->toDateTimeString()
            )
            ->groupBy('accounts.user_id')
            ->orderByDesc('txn_counts')
            ->limit(3)
            ->get();

        $highUsageUserIds = $txnUsage->pluck('user_id');

        $testUser = User::query()
            ->with('accounts.transactions')
            ->whereIn('id', $highUsageUserIds)
            ->get();

        $testUser->map(function (User $user) use ($txnUsage) {
            $userTransactions = collect();
            $user->accounts->each(function (Account $account) use (&$userTransactions) {
                $userTransactions->push(...$account->transactions);
            });
            $user->transactions = $userTransactions->take(10)->toArray();
            $user->txn_counts = $txnUsage->where('user_id', $user->id)->value('txn_counts');
        });

        return UserCollection::make($testUser);
    }
}
