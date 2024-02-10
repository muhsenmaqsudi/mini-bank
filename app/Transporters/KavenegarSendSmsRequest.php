<?php

namespace App\Transporters;

use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Collection;

class KavenegarSendSmsRequest extends Request
{
    protected string $baseUrl = 'https://api.kavenegar.com';
    protected string $path = 'v1/%s/sms/send.json';
    protected string $method = 'GET';

    public function __construct(HttpFactory $http)
    {
        parent::__construct($http);

        $this->setPath(sprintf($this->path, config('services.kavenegar.api_key')));
        $this->query['sender'] = config('services.kavenegar.sender');
    }

    public function fake(?bool $isFailed = false): Collection
    {
        if ($isFailed) {
            return collect([
                'return' => [
                    'status' => 403,
                    'message' => 'کد شناسائی معتبر نمی باشد',
                ],
                'entries' => null,
            ]);
        }

        return collect([
            'return' => [
                'status' => 200,
                'message' => 'تایید شد',
            ],
            'entries' => [
                'messageid' => 1970430810,
                'message' => 'test',
                'status' => 1,
                'statustext' => 'در صف ارسال',
                'sender' => '2000500666',
                'receptor' => '09354922919',
                'date' => 1707582964,
                'cost' => 2280,
            ],
        ]);
    }
}
