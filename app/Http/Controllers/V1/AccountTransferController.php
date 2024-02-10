<?php

namespace App\Http\Controllers\V1;

use App\Events\SuccessfulTransferOccurred;
use App\Features\AccountTransferFeature;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AccountTransferRequest;
use App\Http\Responses\V1\AccountTransferResponse;
use App\Models\Card;
use App\Models\Transaction;
use App\ValueObjects\TransactionType;
use App\ValueObjects\TransferType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountTransferController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(AccountTransferRequest $request, AccountTransferFeature $feature): AccountTransferResponse
    {
        $transaction = $feature->handle($request->toDTO());

        return new AccountTransferResponse(
            trackId: $transaction->track_id,
            message: 'The transfer is successful'
        );
    }
}
