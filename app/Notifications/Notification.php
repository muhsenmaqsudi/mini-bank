<?php

namespace App\Notifications;

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

    public function send(): array
    {
        return $this->channel->send($this->receiver, $this->message);
    }
}
