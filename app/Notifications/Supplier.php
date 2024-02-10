<?php

namespace App\Notifications;

use App\DataObjects\SmsSendResDTO;

interface Supplier
{
    public function send(string $receiver, string $message): SmsSendResDTO;
}
