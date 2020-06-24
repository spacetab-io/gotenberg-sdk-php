<?php

declare(strict_types=1);

namespace Spacetab\Sdk\Gotenberg\Exception;

class UnknownException extends SdkException
{
    public static function unknownError()
    {
        return new self('Unknown error occurred.');
    }
}
