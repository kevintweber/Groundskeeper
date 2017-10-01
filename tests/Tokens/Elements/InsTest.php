<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Ins;
use PHPUnit\Framework\TestCase;

class InsTest extends TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $i = new Ins($configuration, 0, 0, 'ins');
        $this->assertTrue($i->isTransparentElement());
    }
}
