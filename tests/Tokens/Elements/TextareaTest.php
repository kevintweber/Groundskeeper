<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Textarea;
use PHPUnit\Framework\TestCase;

class TextareaTest extends TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $t = new Textarea($configuration, 0, 0, 'textarea');
        $this->assertTrue($t->isInteractiveContent());
    }
}
