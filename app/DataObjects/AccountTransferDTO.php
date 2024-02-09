<?php

namespace App\DataObjects;

use App\Http\Requests\AccountTransferRequest;
use App\ValueObjects\AccountType;

class AccountTransferDTO
{
    public function __construct(
        public readonly string $senderCard,
        public readonly string $receivingCard,
        public readonly int $amount,
        public readonly ?AccountType $type = AccountType::NORMAL
    ) {
    }

    public static function fromRequest(AccountTransferRequest $request): static
    {
        return new static(
            senderCard: $request->input('sender_card'),
            receivingCard:$request->input('receiving_card'),
            amount: $request->integer('amount'),
        );
    }

    public function toArray(): array
    {
        return [
            'sender_card_no' => $this->senderCard,
            'receiving_card_no' => $this->receivingCard,
            'amount' => $this->amount,
            'type' => $this->type->value,
        ];
    }
}
