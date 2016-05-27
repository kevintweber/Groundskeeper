<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Ins;

class InsTest extends \PHPUnit_Framework_TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $i = new Ins($configuration, 0, 0, 'ins');
        $this->assertTrue($i->isTransparentElement());
    }
}
