<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Input;
use PHPUnit\Framework\TestCase;

class InputTest extends TestCase
{
    public function testIsInteractiveContent()
    {
        $configuration = new Configuration();
        $d = new Input($configuration, 0, 0, 'input');
        $this->assertTrue($d->isInteractiveContent());
        $d->addAttribute('type', 'submit');
        $this->assertTrue($d->isInteractiveContent());
        $d->addAttribute('type', 'hidden');
        $this->assertFalse($d->isInteractiveContent());
    }
}
