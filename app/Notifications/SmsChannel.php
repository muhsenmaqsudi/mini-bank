<?php

namespace App\Notifications;

interface SmsChannel
{
    public function send(string $receiver, string $message);
}
