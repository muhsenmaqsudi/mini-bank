<?php

namespace App\Notifications\Suppliers;

use App\DataObjects\SmsSendResDTO;
use App\Notifications\Supplier;
use App\Transporters\KavenegarSendSmsRequest;

class Kavenegar implements Supplier
{
    public function send(string $receiver, string $message): SmsSendResDTO
    {
        $response = KavenegarSendSmsRequest::build()
            ->withQuery([
                'receptor' => $receiver,
                'message' => $message,
            ])
            ->send()
            ->collect();

        $status = $response->get('return')['status'];

        return new SmsSendResDTO(
            status: $status,
            message: $response->get('return')['message']
        );
    }
}
