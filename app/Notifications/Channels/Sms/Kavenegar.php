<?php

namespace App\Notifications\Channels\Sms;

use App\Notifications\SmsChannel;

class Kavenegar implements SmsChannel
{

    public function send(string $receiver, string $message)
    {
        logger()->info(['receiver' => $receiver, 'message' => $message]);
    }
}
