<?php

declare(strict_types=1);

namespace Spacetab\Sdk\Gotenberg\Request;

use ArrayIterator;

final class Html extends ArrayIterator
{
    public function setPaperWidth(float $value): void
    {
        $this['paperWidth'] = $value;
    }

    public function setPaperHeight(float $value): void
    {
        $this['paperHeight'] = $value;
    }

    public function setMarginTop(float $value): void
    {
        $this['marginTop'] = $value;
    }

    public function setMarginBottom(float $value): void
    {
        $this['marginBottom'] = $value;
    }

    public function setMarginLeft(float $value): void
    {
        $this['marginLeft'] = $value;
    }

    public function setMarginRight(float $value): void
    {
        $this['marginRight'] = $value;
    }

    public function setLandscape(bool $value): void
    {
        $this['landscape'] = $value;
    }

    public function setScale(float $value): void
    {
        $this['scale'] = $value;
    }
}
