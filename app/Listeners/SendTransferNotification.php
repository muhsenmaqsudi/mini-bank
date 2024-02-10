<?php

namespace App\Listeners;

use App\Events\SuccessfulTransferOccurred;
use App\Models\Account;
use App\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendTransferNotification implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly Notification $notification)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SuccessfulTransferOccurred $event): void
    {
        /** @var Account $receivingAccount */
        $receivingAccount = Account::query()->find(id: $event->transferable->getReceivingAccount());
        /** @var Account $senderAccount */
        $senderAccount = Account::query()->find(id: $event->transferable->getSenderAccount());
        $this->notification
            ->to($receivingAccount->user->mobile)
            ->withMessage(
                __('notification.transfers.deposit', [
                    'amount' => $event->transferable->amount,
                    'card_no' => $event->transferable->receivingCard,
                ])
            )
            ->send();

        $this->notification
            ->to($senderAccount->user->mobile)
            ->withMessage(
                __('notification.transfers.withdraw', [
                    'amount' => $event->transferable->amount,
                    'card_no' => $event->transferable->senderCard,
                ])
            )
            ->send();
    }
}
