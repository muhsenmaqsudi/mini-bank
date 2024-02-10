<?php

namespace App\Features;

use App\DataObjects\AccountTransferDTO;
use App\Events\SuccessfulTransferOccurred;
use App\Models\Card;
use App\Models\Transaction;
use App\ValueObjects\TransactionType;
use App\ValueObjects\TransferType;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AccountTransferFeature
{
    public function handle(AccountTransferDTO $transferable): Transaction
    {
        $transaction = Cache::lock($transferable->senderCard, 10)->get(function () use ($transferable) {
            /** @var Transaction $transaction */
            return DB::transaction(function () use ($transferable) {
                /** @var Card $senderCard */
                $senderCard = Card::query()->where('card_no', $transferable->senderCard)->firstOrFail();
                $transferable->setSenderAccount($senderCard->account_id);

                /** @var Card $receivingCard */
                $receivingCard = Card::query()->where('card_no', $transferable->receivingCard)->firstOrFail();
                $transferable->setReceivingAccount($receivingCard->account_id);

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

                    $receivingCard->account->update([
                        'balance' => $receivingCard->account->balance + $transferable->amount
                    ]);

                    return $txn;
                };
            });
        });

        SuccessfulTransferOccurred::dispatch($transferable);

        return $transaction;
    }
}
