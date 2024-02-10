<?php

namespace App\Transporters;

use BadMethodCallException;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Pool;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;
use Illuminate\Support\Traits\Macroable;
use OutOfBoundsException;
use RuntimeException;

abstract class Request
{
    use Macroable {
        __call as macroCall;
    }

    protected PendingRequest $request;
    protected array $pendingRequestCalls = [];

    protected string $method;
    protected string $path;
    protected string $baseUrl;

    protected null|string $as = null;
    protected array $query = [];
    protected array $data = [];
    protected array $headers = [];

    protected int $status;

    private HttpFactory $http;

    public static function build(...$args): static
    {
        return app(static::class, $args);
    }

    /**
     * @param  HttpFactory  $http
     */
    public function __construct(HttpFactory $http)
    {
        $this->http = $http;
    }

    /**
     * @return string|null
     */
    public function getAs(): null|string
    {
        return $this->as;
    }

    /**
     * @param  string|int  $as
     * @return static
     */
    public function as(string|int $as): static
    {
        $this->as = $as;

        return $this;
    }

    /**
     * @param  array  $data
     * @return static
     */
    public function withData(array $data): static
    {
        $this->data = array_merge($this->data, $data);

        return $this;
    }

    public function setHeaders(array $headers): static
    {
        $this->headers = array_merge($this->headers, $headers);

        return $this;
    }

    /**
     * @return array
     */
    public function payload(): array
    {
        return $this->data;
    }

    /**
     * @param  array  $query
     * @return static
     */
    public function withQuery(array $query): static
    {
        $this->query = array_merge_recursive($this->query, $query);

        return $this;
    }

    /**
     * @return string
     *
     * @throws RuntimeException
     */
    public function getBaseUrl(): string
    {
        if (isset($this->baseUrl)) {
            return $this->baseUrl;
        }

        throw new RuntimeException(
            message: 'Neither a baseUrl or a config base_uri has been set for this request.',
        );
    }

    /**
     * @param  string  $baseUrl
     * @return static
     */
    public function lockOn(string $baseUrl): static
    {
        return $this->setBaseUrl($baseUrl);
    }

    /**
     * @param  string  $baseUrl
     * @return static
     */
    public function setBaseUrl(string $baseUrl): static
    {
        $this->baseUrl = $baseUrl;

        if (isset($this->request)) {
            $this->request->baseUrl($baseUrl);
        }

        return $this;
    }

    /**
     * @param  Pool  $pool
     * @return \GuzzleHttp\Promise\PromiseInterface|Response
     */
    public function buildForConcurrent(Pool $pool): \GuzzleHttp\Promise\PromiseInterface|Response
    {
        $this->ensureRequest($pool);

        return $this->buildRequest();
    }

    private function buildRequest(): \GuzzleHttp\Promise\PromiseInterface|Response
    {
        return match (mb_strtoupper($this->method)) {
            'GET' => $this->request->get($this->getUrl(), $this->query),
            'POST' => $this->request->post($this->getUrl(), $this->data),
            'PUT' => $this->request->put($this->getUrl(), $this->data),
            'PATCH' => $this->request->patch($this->getUrl(), $this->data),
            'DELETE' => $this->request->delete($this->getUrl(), $this->data),
            'HEAD' => $this->request->head($this->getUrl(), $this->query),
            default => throw new OutOfBoundsException()
        };
    }

    /**
     * @return Response
     */
    public function energize(): Response
    {
        return $this->send();
    }

    /**
     * @return Response
     */
    public function send(): Response
    {
        $this->ensureRequest();

        return $this->buildRequest();
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        $url = (string) Str::of($this->path())
            ->when(
                ! empty($this->query),
                fn (Stringable $path): Stringable => $path->append('?', http_build_query($this->query))
            );
        if (Str::of($this->method)->upper()->contains('GET', 'HEAD')) {
            return $this->path();
        }

        return $url;
    }

    /**
     * @param  PendingRequest  $request
     * @return void
     */
    protected function withRequest(PendingRequest $request): void
    {
        // do something with the initialized request
    }

    /**
     * @return string
     */
    public function path(): string
    {
        return $this->path ?? '';
    }

    /**
     * @param  string  $path
     * @return static
     */
    public function setPath(string $path): static
    {
        $this->path = $path;

        return $this;
    }

    public function appendPath(string $appends): static
    {
        $this->path = "{$this->path}/{$appends}";

        return $this;
    }

    /**
     * @return PendingRequest
     */
    public function getRequest(): PendingRequest
    {
        $this->ensureRequest();

        return $this->request;
    }

    /**
     * @return array
     */
    public function getQuery(): array
    {
        return $this->query;
    }

    /**
     * @param  Pool|null  $pool
     */
    private function ensureRequest(null|Pool $pool = null): void
    {
        if (! isset($this->request)) {
            if ($pool === null) {
                $this->request = $this->http->baseUrl(
                    url: $this->baseUrl ?? '',
                );
            } else {
                $this->request = match ($this->as === null) {
                    false => $pool->as(key: $this->as)->baseUrl($this->baseUrl ?? ''),
                    true => $pool->baseUrl(url: $this->baseUrl ?? '')
                };
            }

            $this->withRequest($this->request);

            foreach ($this->pendingRequestCalls as $call) {
                call_user_func_array([$this->request, $call[0]], $call[1]);
            }
        }
    }

    /**
     * @param  string  $method
     * @param  array  $parameters
     * @return static
     */
    public function __call($method, $parameters): static
    {
        if (isset($this->request)) {
            if (method_exists($this->request, $method)) {
                call_user_func_array([$this->request, $method], $parameters);

                return $this;
            }
        } else {
            if (method_exists(PendingRequest::class, $method)) {
                $this->pendingRequestCalls[] = [$method, $parameters];

                return $this;
            }
        }

        throw new BadMethodCallException();
    }
}
