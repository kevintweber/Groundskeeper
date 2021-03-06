<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\A;
use PHPUnit\Framework\TestCase;

class ATest extends TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $a = new A($configuration, 0, 0, 'a');
        $this->assertTrue($a->isTransparentElement());
    }
}
