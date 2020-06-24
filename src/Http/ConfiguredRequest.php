<?php

declare(strict_types=1);

namespace Spacetab\Sdk\Gotenberg\Http;

use Amp\Http\Client\Request;

class ConfiguredRequest
{
    private string $baseUri;

    /**
     * ConfiguredRequest constructor.
     *
     * @param string $baseUri
     */
    public function __construct(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @param \Psr\Http\Message\UriInterface|string $uri
     * @param string $method
     * @param null $body
     * @return \Amp\Http\Client\Request
     */
    public function makeRequest(string $uri, string $method = 'GET', $body = null)
    {
        $request = new Request($this->baseUri . $uri, $method);
        $request->setBody($body);

        return $request;
    }
}
