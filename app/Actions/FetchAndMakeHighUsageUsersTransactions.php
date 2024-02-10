<?php

namespace App\Actions;

use App\Models\Account;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class FetchAndMakeHighUsageUsersTransactions
{
    public static function handle($highUsageUserIds, $txnUsage): Collection|array
    {
        $usersWithHighTransactions = User::query()
            ->with('accounts.transactions')
            ->whereIn('id', $highUsageUserIds)
            ->get();

        $usersWithHighTransactions->map(function (User $user) use ($txnUsage) {
            $userTransactions = collect();
            $user->accounts->each(function (Account $account) use (&$userTransactions) {
                $userTransactions->push(...$account->transactions);
            });
            $user->transactions = $userTransactions->take(10)->toArray();
            $user->txn_counts = $txnUsage->where('user_id', $user->id)->value('txn_counts');
        });

        return $usersWithHighTransactions;
    }
}
