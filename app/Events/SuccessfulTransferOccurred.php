<?php

namespace App\Events;

use App\DataObjects\AccountTransferDTO;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SuccessfulTransferOccurred
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public AccountTransferDTO $transferable)
    {
        //
    }
}
