<?php

declare(strict_types=1);

namespace Spacetab\Sdk\Gotenberg;

use Amp\Promise;
use Amp\File;
use Amp\ByteStream;
use function Amp\call;

final class Output
{
    private ByteStream\InputStream $stream;

    public function __construct(ByteStream\InputStream $stream)
    {
        $this->stream = $stream;
    }

    public function asStream(): ByteStream\InputStream
    {
        return $this->stream;
    }

    public function asFile(string $path): Promise
    {
        if (!array_key_exists('extension', pathinfo($path))) {
            throw new \InvalidArgumentException('Extension will require to saving file.');
        }

        return call(function () use ($path) {
            /** @var \Amp\File\File $file */
            $file = yield File\open($path, "w");

            while (null !== $chunk = yield $this->asStream()->read()) {
                yield $file->write($chunk);
            }

            yield $file->close();
        });
    }

    public function asString(): Promise
    {
        return call(fn() => yield ByteStream\buffer($this->asStream()));
    }
}
