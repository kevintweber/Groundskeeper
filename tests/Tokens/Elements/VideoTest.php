<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Video;
use PHPUnit\Framework\TestCase;

class VideoTest extends TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $v = new Video($configuration, 0, 0, 'video');
        $this->assertFalse($v->isInteractiveContent());
        $this->assertTrue($v->isTransparentElement());
    }
}
