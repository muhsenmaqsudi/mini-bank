<?php

namespace App\Http\Controllers\V1;

use App\Features\AccountTransferFeature;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AccountTransferRequest;
use App\Http\Responses\V1\AccountTransferResponse;
use App\Http\Responses\V1\ErrorResponse;
use Illuminate\Contracts\Support\Responsable;

class AccountTransferController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(AccountTransferRequest $request, AccountTransferFeature $feature): Responsable
    {
        try {
            $transaction = $feature->handle($request->toDTO());

            return new AccountTransferResponse(
                trackId: $transaction->track_id,
                message: 'The transfer is successful'
            );
        } catch (\Throwable $th) {
            return new ErrorResponse(
                message: $th->getMessage(),
                exception: $th,
                code: 10030 // TODO: add all error codes
            );
        }
    }
}
