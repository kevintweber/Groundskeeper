<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Button;

class ButtonTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInteractiveContent()
    {
        $configuration = new Configuration();
        $a = new Button($configuration, 0, 0, 'button');
        $this->assertTrue($a->isInteractiveContent());
    }
}
