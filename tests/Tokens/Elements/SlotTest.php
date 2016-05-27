<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Slot;

class SlotTest extends \PHPUnit_Framework_TestCase
{
    public function testSlot()
    {
        $configuration = new Configuration();
        $v = new Slot($configuration, 0, 0, 'slot');
        $this->assertTrue($v->isTransparentElement());
    }
}
