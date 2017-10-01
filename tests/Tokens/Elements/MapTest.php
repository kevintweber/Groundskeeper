<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Map;
use PHPUnit\Framework\TestCase;

class MapTest extends TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $m = new Map($configuration, 0, 0, 'map');
        $this->assertTrue($m->isTransparentElement());
    }
}
