<?php

declare(strict_types=1);

namespace Spacetab\Sdk\Gotenberg\Capabilities;

use Amp\Promise;

interface HtmlInterface
{
    /**
     * @param string $html
     * @param iterable|\Spacetab\Sdk\Gotenberg\Request\Html $options
     * @return \Amp\Promise
     */
    public function fromString(string $html, iterable $options = []): Promise;
}
