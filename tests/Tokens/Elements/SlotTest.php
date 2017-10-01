<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Slot;
use PHPUnit\Framework\TestCase;

class SlotTest extends TestCase
{
    public function testSlot()
    {
        $configuration = new Configuration();
        $v = new Slot($configuration, 0, 0, 'slot');
        $this->assertTrue($v->isTransparentElement());
    }
}
