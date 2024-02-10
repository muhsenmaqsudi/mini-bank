<?php

namespace App\Notifications\Suppliers;

use App\Notifications\Supplier;
use App\Transporters\GhasedakSendSmsRequest;

class Ghasedak implements Supplier
{
    public function send(string $receiver, string $message): array
    {
        $response = GhasedakSendSmsRequest::build()
            ->withData([
                'receptor' => $receiver,
                'message' => $message,
            ])
            ->send()
            ->collect();

        $status = $response->get('result')['code'];
        return [
            'is_done' => $status == 200,
            'status' => $status,
            'message' => $response->get('result')['message'],
        ];
    }
}
