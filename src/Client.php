<?php

declare(strict_types=1);

namespace Spacetab\Sdk\Gotenberg;

use Amp\Http\Client\HttpClient;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Spacetab\Sdk\Gotenberg\Capabilities\Html;
use Spacetab\Sdk\Gotenberg\Http\ConfiguredRequest;
use Spacetab\Sdk\Gotenberg\Http\HttpClientConfigurator;

class Client implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public const VERSION = '1.0.0b';

    private HttpClientConfigurator $clientConfigurator;
    private HttpClient $httpClient;
    private ConfiguredRequest $httpRequest;

    /**
     * Client constructor.
     *
     * @param \Spacetab\Sdk\Gotenberg\Http\HttpClientConfigurator $clientConfigurator
     * @param \Psr\Log\LoggerInterface|null $logger
     */
    public function __construct(HttpClientConfigurator $clientConfigurator, ?LoggerInterface $logger = null)
    {
        $this->clientConfigurator = $clientConfigurator;
        $this->httpClient         = $clientConfigurator->createConfiguredHttpClient();
        $this->httpRequest        = $clientConfigurator->createConfiguredHttpRequest();
        $this->logger             = $logger ?: new NullLogger();
    }

    public static function withBasicAuth(string $endpoint, string $basicUsername, string $basicPassword): self
    {
        $configurator = (new HttpClientConfigurator())
            ->setEndpoint($endpoint)
            ->setBasicUsername($basicUsername)
            ->setBasicPassword($basicPassword);

        return new Client($configurator);
    }

    public static function new(string $endpoint): self
    {
        $configurator = (new HttpClientConfigurator())
            ->setEndpoint($endpoint);

        return new Client($configurator);
    }

    public function html(): Capabilities\HtmlInterface
    {
        return new Html($this->httpClient, $this->httpRequest, $this->logger);
    }
}
