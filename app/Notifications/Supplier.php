<?php

namespace App\Notifications;

interface Supplier
{
    public function send(string $receiver, string $message): array;
}
