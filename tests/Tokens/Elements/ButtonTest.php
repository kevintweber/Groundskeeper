<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Button;
use PHPUnit\Framework\TestCase;

class ButtonTest extends TestCase
{
    public function testIsInteractiveContent()
    {
        $configuration = new Configuration();
        $a = new Button($configuration, 0, 0, 'button');
        $this->assertTrue($a->isInteractiveContent());
    }
}
