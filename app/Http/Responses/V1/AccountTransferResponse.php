<?php

namespace App\Http\Responses\V1;

use App\ValueObjects\HttpStatus;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountTransferResponse implements Responsable
{
    public function __construct(
        private readonly string $trackId,
        private readonly string $message,
        private readonly HttpStatus $status = HttpStatus::OK
    ) {
    }

    /**
     * @param Request $request
     */
    public function toResponse($request): JsonResponse
    {
        return response()->json([
            'status' => $this->status === HttpStatus::OK,
            'message' => $this->message,
            'track_id' => $this->trackId,
        ]);
    }
}
