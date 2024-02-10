<?php

namespace App\Notifications;

use App\DataObjects\SmsSendResDTO;

class Notification
{
    private string $receiver;
    private string $message;

    public function __construct(private readonly Supplier $channel)
    {
    }

    public function to(string $mobile): static
    {
        $this->receiver = $mobile;
        return $this;
    }

    public function withMessage(string $message): static
    {
        $this->message = $message;
        return $this;
    }

    public function send(): SmsSendResDTO
    {
        return $this->channel->send($this->receiver, $this->message);
    }
}
