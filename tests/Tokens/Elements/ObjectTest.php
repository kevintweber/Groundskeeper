<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Object;

class ObjectTest extends \PHPUnit_Framework_TestCase
{
    public function testObject()
    {
        $configuration = new Configuration();
        $v = new Object($configuration, 0, 0, 'object');
        $this->assertTrue($v->isInteractiveContent());
        $this->assertTrue($v->isTransparentElement());
    }
}
