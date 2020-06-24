<?php

declare(strict_types=1);

namespace Spacetab\Sdk\Gotenberg\Capabilities;

use Amp\Http\Client\Body\FormBody;
use Amp\File;
use Amp\Promise;
use Spacetab\Sdk\Gotenberg\Http\HttpAPI;
use Spacetab\Sdk\Gotenberg\Output;

use function Amp\call;

class Html extends HttpAPI implements HtmlInterface
{
    public function fromString(string $html, iterable $options = []): Promise
    {
        return call(function () use ($html, $options) {
            $path = yield from $this->createTemporaryIndexFile($html);
            $body = new FormBody();

            foreach ($options as $field => $value) {
                $body->addField($field, (string) $value);
            }

            $body->addFile('index.html', $path);

            return new Output(yield $this->httpPost('/convert/html', $body));
        });
    }

    /**
     * Workaround for amphp/http-client because
     * it FormBody object does not support in-memory files.
     *
     * @param string $html
     * @return \Generator
     */
    private function createTemporaryIndexFile(string $html): \Generator
    {
        $path = sys_get_temp_dir() . '/index.html';

        /** @var \Amp\File\File $file */
        $file = yield File\open($path, 'w');
        yield $file->write($html);
        yield $file->close();

        return $path;
    }
}
