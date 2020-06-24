<?php

declare(strict_types=1);

namespace Spacetab\Sdk\Gotenberg\Http;

use Amp\Http\Client\Connection\UnlimitedConnectionPool;
use Amp\Http\Client\HttpClient;
use Amp\Http\Client\InterceptedHttpClient;
use Amp\Http\Client\Interceptor\DecompressResponse;
use Amp\Http\Client\Interceptor\FollowRedirects;
use Amp\Http\Client\Interceptor\LogHttpArchive;
use Amp\Http\Client\Interceptor\RetryRequests;
use Amp\Http\Client\Interceptor\SetRequestHeaderIfUnset;
use Amp\Http\Client\PooledHttpClient;
use Spacetab\Sdk\Gotenberg\Client;

final class HttpClientConfigurator
{
    /**
     * Retry requests count.
     */
    private const RETRY_REQUESTS   = 5;
    private const FOLLOW_REDIRECTS = 10;

    private string $basicAuthUsername = '';
    private string $basicAuthPassword = '';
    private string $endpoint;

    private UnlimitedConnectionPool $pool;

    /**
     * HttpClientConfigurator constructor.
     */
    public function __construct()
    {
        $this->pool = new UnlimitedConnectionPool;
    }

    /**
     * @return \Amp\Http\Client\HttpClient
     */
    public function createConfiguredHttpClient(): HttpClient
    {
        $client = new PooledHttpClient($this->pool);

        $interceptors = [
            new SetRequestHeaderIfUnset('User-Agent', sprintf('Gotenberg PHP Async SDK / v%s', Client::VERSION)),
        ];

        if ($this->isRequestWithAnAuthorization()) {
            $interceptors[] = new SetRequestHeaderIfUnset('Authorization', $this->getAuthorizationHeaderValue());
        }

        foreach ($interceptors as $interceptor) {
            $client = $client->intercept($interceptor);
        }

        $client = new InterceptedHttpClient($client, new RetryRequests(self::RETRY_REQUESTS));
        $client = new InterceptedHttpClient($client, new FollowRedirects(self::FOLLOW_REDIRECTS));

        return new HttpClient($client);
    }

    public function createConfiguredHttpRequest(): ConfiguredRequest
    {
        return (new ConfiguredRequest($this->endpoint));
    }

    public function setEndpoint(string $endpoint): self
    {
        $this->endpoint = rtrim($endpoint, '/');

        return $this;
    }

    public function setBasicUsername(string $username): self
    {
        $this->basicAuthUsername = $username;

        return $this;
    }

    public function setBasicPassword(string $password): self
    {
        $this->basicAuthPassword = $password;

        return $this;
    }

    private function getAuthorizationHeaderValue(): string
    {
        return 'Basic ' . base64_encode("$this->basicAuthUsername:$this->basicAuthPassword");
    }

    private function isRequestWithAnAuthorization(): bool
    {
        return strlen($this->basicAuthUsername) > 0 && strlen($this->basicAuthPassword) > 0;
    }
}
