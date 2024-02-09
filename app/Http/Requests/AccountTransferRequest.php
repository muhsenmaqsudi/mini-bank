<?php

namespace App\Http\Requests;

use App\DataObjects\AccountTransferDTO;
use App\Rules\CardNumber;
use Illuminate\Foundation\Http\FormRequest;

class AccountTransferRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sender_card' => ['required', 'digits:16', new CardNumber(), 'bail'],
            'receiving_card' => ['required', 'digits:16', new CardNumber(), 'bail'],
            'amount' => 'required|numeric|between:10000,500000000|bail',
        ];
    }

    public function toDTO(): AccountTransferDTO
    {
        return AccountTransferDTO::fromRequest($this);
    }
}
