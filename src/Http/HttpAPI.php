<?php

declare(strict_types=1);

namespace Spacetab\Sdk\Gotenberg\Http;

use Amp\Http\Client\Body\FormBody;
use Amp\Http\Client\HttpClient;
use Amp\Http\Client\Response;
use Amp\Promise;
use Kelunik\Retry\ConstantBackoff;
use Psr\Log\LoggerInterface;
use Spacetab\Sdk\Gotenberg\Exception\ResponseException;
use Spacetab\Sdk\Gotenberg\Exception\UnknownException;
use function Amp\call;
use function Kelunik\Retry\retry;

abstract class HttpAPI
{
    private const REQUEST_RETRY_ATTEMPTS = 10;
    private const REQUEST_RETRY_DELAY    = 500;

    protected LoggerInterface $logger;
    protected HttpClient $httpClient;
    protected ConfiguredRequest $configuredRequest;

    /**
     * HttpAPI constructor.
     *
     * @param \Amp\Http\Client\HttpClient $httpClient
     * @param \Spacetab\Sdk\Gotenberg\Http\ConfiguredRequest $configuredRequest
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(HttpClient $httpClient, ConfiguredRequest $configuredRequest, LoggerInterface $logger)
    {
        $this->httpClient        = $httpClient;
        $this->configuredRequest = $configuredRequest;
        $this->logger            = $logger;
    }

    /**
     * Sends a GET HTTP Request with query parameters.
     *
     * @param string $path
     * @param \Amp\Http\Client\Body\FormBody $body
     * @return \Amp\Promise
     */
    protected function httpPost(string $path, FormBody $body): Promise
    {
        return $this->retry(function () use ($path, $body) {
            $bodyLength = yield $body->getBodyLength();

            $this->logger->debug("Send POST request to {$path}", compact('bodyLength'));

            /** @var \Amp\Http\Client\Response $response */
            $response = yield $this->httpClient->request(
                $this->configuredRequest->makeRequest($path, 'POST', $body)
            );

            return $this->handleResponse($response, $path, $bodyLength);
        });
    }

    /**
     * @param \Amp\Http\Client\Response $response
     * @param string $path
     * @param int|null $bodyLength
     * @return \Amp\Promise
     */
    protected function handleResponse(Response $response, string $path, ?int $bodyLength = null): Promise
    {
        return call(function () use ($response, $path, $bodyLength) {
            $this->logger->debug("Received a response for {$path} with status code: {$response->getStatus()}");

            $continue = false;
            switch (true) {
                case $response->getStatus() >= 200 && $response->getStatus() <= 299:
                    $continue = true;
                    break;
                case $response->getStatus() === 400:
                    $exception = ResponseException::badRequest();
                    break;
                case $response->getStatus() === 401:
                    $exception = ResponseException::unauthorized();
                    break;
                case $response->getStatus() === 402:
                    $exception = ResponseException::requestFailed();
                    break;
                case $response->getStatus() === 403:
                    $exception = ResponseException::forbidden();
                    break;
                case $response->getStatus() === 404:
                    $exception = ResponseException::notFound();
                    break;
                case $response->getStatus() === 413:
                    $exception = ResponseException::payloadTooLarge();
                    break;
                case $response->getStatus() >= 500 && $response->getStatus() <= 599:
                    $exception = ResponseException::serverError();
                    break;
                default:
                    $exception = UnknownException::unknownError();
            }

            if ($continue) {
                $this->logger->debug("Response for {$path} is correct, return a parsed server value...");
                return $response->getBody();
            }

            $this->logger->info("Response for {$path} incorrect, stops the request...", compact('bodyLength'));

            throw $exception;
        });
    }

    /**
     * @param callable $callback
     * @return \Amp\Promise
     */
    protected function retry(callable $callback): Promise
    {
        return retry(self::REQUEST_RETRY_ATTEMPTS, $callback, \Exception::class, new ConstantBackoff(self::REQUEST_RETRY_DELAY));
    }
}
