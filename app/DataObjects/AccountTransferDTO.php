<?php

namespace App\DataObjects;

use App\Http\Requests\V1\AccountTransferRequest;
use App\Models\Card;
use App\ValueObjects\AccountType;

class AccountTransferDTO
{
    private string $receivingAccount;
    private string $senderAccount;

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

    public function getReceivingAccount(): string
    {
        return $this->receivingAccount;
    }

    public function setReceivingAccount(string $receivingAccount): void
    {
        $this->receivingAccount = $receivingAccount;
    }

    public function getSenderAccount(): string
    {
        return $this->senderAccount;
    }

    public function setSenderAccount(string $senderAccount): void
    {
        $this->senderAccount = $senderAccount;
    }
}
