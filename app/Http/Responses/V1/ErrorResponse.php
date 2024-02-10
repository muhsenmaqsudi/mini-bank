<?php

namespace App\Http\Responses\V1;

use App\ValueObjects\HttpStatus;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class ErrorResponse implements Responsable
{
    public function __construct(
        protected readonly string     $message = 'Something went wrong',
        protected readonly array      $data = [],
        protected readonly ?Throwable $exception = null,
        protected readonly HttpStatus $httpStatus = HttpStatus::INTERNAL_SERVER_ERROR,
        protected readonly int $code = 0,
        protected readonly array      $headers = [],
    ) {
    }

    public function toResponse($request): JsonResponse
    {
        $response = [
            'success' => false,
            'message' => $this->message,
            'data' => $this->data,
            'code' => $this->code,
        ];

        if (null !== $this->exception && config('app.debug')) {
            $response['debug'] = [
                'message' => $this->exception->getMessage(),
                'file' => $this->exception->getFile(),
                'line' => $this->exception->getLine(),
                'trace' => $this->exception->getTraceAsString(),
            ];
        }

        return response()->json(
            data: $response,
            status: $this->httpStatus->value,
        );
    }
}
