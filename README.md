Asynchronous PHP Gotenberg SDK
------------------------------

A simple PHP async client for interacting with a Gotenberg API. Based on [Amphp](https://amphp.org).

Util `1.0.0` release library is unstable. Not ready for production use. 

## Install

```bash
composer require spacetab-io/gotenberg-sdk
```

## Usage example

### Simple methods which returns a promise

```php
use Amp\Loop;
use Spacetab\Logger\Logger;
use Psr\Log\LogLevel;
use Spacetab\Sdk\Gotenberg;
use Spacetab\Sdk\Gotenberg\Request;

require __DIR__ . '/vendor/autoload.php';

Loop::run(function () use ($html) {
    $logger = Logger::default('Gotenberg', LogLevel::DEBUG);
    $gotenberg = Gotenberg\Client::new('http://0.0.0.0:3000');
    $gotenberg->setLogger($logger);

    $option = new Request\Html();
    $option->setLandscape(true);
    $option->setMarginBottom(0.1);
    $option->setMarginLeft(0.1);
    $option->setMarginRight(0.1);
    $option->setMarginTop(0.1);
    $option->setScale(0.70);

    /** @var \Spacetab\Sdk\Gotenberg\Output $output */
    $output = yield $gotenberg->html()->fromString('<p>hi</p>', $option);
    yield $output->asFile(__DIR__ . '/index.pdf');
});
```

## Supported methods

* Converts from HTML to PDF

## License

The MIT License

Copyright © 2020 spacetab.io, Inc. https://spacetab.io

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
