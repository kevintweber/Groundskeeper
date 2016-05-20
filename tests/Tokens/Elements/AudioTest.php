<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Audio;

class AudioTest extends \PHPUnit_Framework_TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $a = new Audio($configuration, 0, 0, 'audio');
        $this->assertFalse($a->isInteractiveContent());
        $this->assertTrue($a->isTransparentElement());
    }
}
