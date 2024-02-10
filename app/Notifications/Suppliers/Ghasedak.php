<?php

namespace App\Notifications\Suppliers;

use App\DataObjects\SmsSendResDTO;
use App\Notifications\Supplier;
use App\Transporters\GhasedakSendSmsRequest;

class Ghasedak implements Supplier
{
    public function send(string $receiver, string $message): SmsSendResDTO
    {
        $response = GhasedakSendSmsRequest::build()
            ->withData([
                'receptor' => $receiver,
                'message' => $message,
            ])
            ->send()
            ->collect();

        $status = $response->get('result')['code'];

        return new SmsSendResDTO(
            status: $status,
            message: $response->get('result')['message']
        );
    }
}
