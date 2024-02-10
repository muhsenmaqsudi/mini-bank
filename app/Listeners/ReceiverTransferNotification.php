<?php

namespace App\Listeners;

use App\Events\SuccessfulTransferOccurred;
use App\Models\Account;
use App\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class ReceiverTransferNotification implements ShouldQueue
{
    use InteractsWithQueue;
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

        $result = $this->notification
            ->to($receivingAccount->user->mobile)
            ->withMessage(
                __('notification.transfers.deposit', [
                    'amount' => $event->transferable->amount,
                    'card_no' => $event->transferable->receivingCard,
                ])
            )
            ->send();

        if (! $result['is_done']) {
            $this->job->fail();
        }
    }
}
