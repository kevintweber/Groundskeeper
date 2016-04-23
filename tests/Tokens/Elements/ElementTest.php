<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Tokens\Elements\Element;

class ElementTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $element = new Element('asdf');
        $this->assertEmpty($element->getAttributes());
        $this->assertEmpty($element->getChildren());
        $this->assertEquals('asdf', $element->getName());
    }
}

