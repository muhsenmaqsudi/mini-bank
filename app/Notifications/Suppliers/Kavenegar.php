<?php

namespace App\Notifications\Suppliers;

use App\Notifications\Supplier;
use App\Transporters\KavenegarSendSmsRequest;

class Kavenegar implements Supplier
{

    public function send(string $receiver, string $message): array
    {
        $response = KavenegarSendSmsRequest::build()
            ->withQuery([
                'receptor' => $receiver,
                'message' => $message
            ])
            ->send()
            ->collect();

        $status = $response->get('return')['status'];
        return [
            'is_done' => $status == 200,
            'status' => $status,
            'message' => $response->get('return')['message'],
        ];
    }
}
