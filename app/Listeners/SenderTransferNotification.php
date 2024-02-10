<?php

namespace App\Listeners;

use App\Events\SuccessfulTransferOccurred;
use App\Models\Account;
use App\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SenderTransferNotification implements ShouldQueue
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
        /** @var Account $senderAccount */
        $senderAccount = Account::query()->find(id: $event->transferable->getSenderAccount());

        $result = $this->notification
            ->to($senderAccount->user->mobile)
            ->withMessage(
                __('notification.transfers.withdraw', [
                    'amount' => $event->transferable->amount,
                    'card_no' => $event->transferable->senderCard,
                ])
            )
            ->send();

        if (! $result['is_done']) {
            $this->job->fail();
        }
    }
}
