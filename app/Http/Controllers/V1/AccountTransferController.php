<?php

namespace App\Http\Controllers\V1;

use App\Events\SuccessfulTransferOccurred;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\AccountTransferRequest;
use App\Models\Card;
use App\Models\Transaction;
use App\ValueObjects\TransactionType;
use App\ValueObjects\TransferType;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountTransferController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(AccountTransferRequest $request): JsonResponse
    {
        $transferable = $request->toDTO();

        /** @var Transaction $transaction */
        $transaction = DB::transaction(function () use ($transferable) {
            /** @var Card $senderCard */
            $senderCard = Card::query()->where('card_no', $transferable->senderCard)->first();

            $transferable->setSenderAccount($senderCard->account_id);

            if ($transferable->amount <= $senderCard->account->balance) {
                /** @var Transaction $txn */
                $txn = Transaction::query()->create([
                    'track_id' => Str::uuid()->toString(),
                    'card_id' => $senderCard->id,
                    'type' => TransactionType::TRANSFER->value,
                    'amount' => $transferable->amount,
                ]);

                $txn->transfers()->create([
                    'sender_id' => $transferable->senderCard,
                    'receiving_id' => $transferable->receivingCard,
                    'amount' => $transferable->amount,
                    'type' => TransferType::WITHDRAW->value,
                ]);
                $txn->transfers()->create([
                    'sender_id' => $transferable->receivingCard,
                    'receiving_id' => $transferable->senderCard,
                    'amount' => $transferable->amount,
                    'type' => TransferType::DEPOSIT->value,
                ]);

                $txn->wage()->create([
                    'fee' => 5000
                ]);

                $senderCard->account->update([
                    'balance' => $senderCard->account->balance - $transferable->amount - 5000
                ]);

                /** @var Card $receivingCard */
                $receivingCard = Card::query()->where('card_no', $transferable->receivingCard)->first();

                $transferable->setReceivingAccount($receivingCard->account_id);

                $receivingCard->account->update([
                    'balance' => (int)$receivingCard->account->balance + $transferable->amount
                ]);

                return $txn;
            };
        });

        SuccessfulTransferOccurred::dispatch($transferable);

        return response()->json([
            'status' => true,
            'message' => 'The transfer is successful',
            'track_id' => $transaction->track_id,
        ]);
    }
}
