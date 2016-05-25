<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Textarea;

class TextareaTest extends \PHPUnit_Framework_TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $t = new Textarea($configuration, 0, 0, 'textarea');
        $this->assertTrue($t->isInteractiveContent());
    }
}
