<?php

declare(strict_types=1);

namespace Spacetab\Sdk\Gotenberg\Exception;

class ResponseException extends SdkException
{
    public static function badRequest()
    {
        return new self('Bad request.');
    }

    public static function unauthorized()
    {
        return new self('Unauthorized. Wrong credentials?');
    }

    public static function requestFailed()
    {
        return new self('Request failed.');
    }

    public static function forbidden()
    {
        return new self('Forbidden.');
    }

    public static function notFound()
    {
        return new self('Not found.');
    }

    public static function payloadTooLarge()
    {
        return new self('Payload too large.');
    }

    public static function serverError()
    {
        return new self('Server error.');
    }
}
