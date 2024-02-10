<?php

namespace App\Http\Controllers\V1;

use App\Actions\FetchAndMakeHighUsageUsersTransactions;
use App\Http\Controllers\Controller;
use App\Http\Resources\V1\UserCollection;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\Read\TransactionRepository;
use Illuminate\Http\Request;

class TransactionTopUsageController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request, TransactionRepository $repository): UserCollection
    {
        $txnUsage = $repository->highUserTransactionsForGivenTime(
            now()->subMinutes(15)->toDateTimeString(),
            '3'
        );

        $highUsageUserIds = $txnUsage->pluck('user_id');

        $usersWithHighTransactions = FetchAndMakeHighUsageUsersTransactions::handle(
            $highUsageUserIds,
            $txnUsage,
        );

        return UserCollection::make($usersWithHighTransactions);
    }
}
