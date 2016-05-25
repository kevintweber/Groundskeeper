<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Map;

class MapTest extends \PHPUnit_Framework_TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $m = new Map($configuration, 0, 0, 'map');
        $this->assertTrue($m->isTransparentElement());
    }
}
