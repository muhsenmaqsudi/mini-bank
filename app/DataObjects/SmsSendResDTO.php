<?php

namespace App\DataObjects;

class SmsSendResDTO
{
    public bool $isDone;
    public function __construct(
        public readonly string $status,
        public readonly string $message
    ) {
        $this->isDone = $status == 200;
    }
}
