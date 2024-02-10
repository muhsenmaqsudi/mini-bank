<?php

namespace App\Transporters;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Collection;

class GhasedakSendSmsRequest extends Request
{
    protected string $baseUrl = 'https://api.ghasedak.me/';
    protected string $path = 'v2/sms/send/simple';
    protected string $method = 'POST';

    public function __construct(HttpFactory $http)
    {
        parent::__construct($http);

        $this->data['linenumber'] = config('services.ghasedak.line_number');
    }

    protected function withRequest(PendingRequest $request): void
    {
        $request->asForm()
            ->withHeaders([
                'apikey' => config('services.ghasedak.api_key'),
            ]);
    }

    public function fake(?bool $isFailed = false): Collection
    {
        if ($isFailed) {
            return collect([
                'result' => [
                    'code' => 401,
                    'message' => 'apikey is invalid',
                ],
                'items' => null,
            ]);
        }

        return collect([
            'result' => [
                'code' => 200,
                'message' => 'success',
            ],
            'items' => [
                3444558635,
            ],
        ]);
    }
}
