<?php

namespace App\Notifications\Channels\Sms;

use App\Notifications\SmsChannel;

class Ghasedak implements SmsChannel
{

    public function send(string $receiver, string $message)
    {
        logger()->warning(['receiver' => $receiver, 'message' => $message]);
    }
}
