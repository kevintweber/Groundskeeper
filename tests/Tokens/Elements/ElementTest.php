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

    public function testAttributes()
    {
        $element = new Element('asdf');
        $this->assertFalse($element->hasAttribute('class'));
        $this->assertEmpty($element->getAttributes());
        $element->addAttribute('class', 'c-asdf');
        $this->assertTrue($element->hasAttribute('class'));
        $this->assertEquals(1, count($element->getAttributes()));
        $element->addAttribute('id', 'i-asdf');
        $this->assertEquals(2, count($element->getAttributes()));
        $this->assertTrue($element->removeAttribute('class'));
        $this->assertFalse($element->removeAttribute('class'));
        $this->assertFalse($element->hasAttribute('class'));
        $this->assertEquals(1, count($element->getAttributes()));
        $this->assertEquals(
            array('id' => 'i-asdf'),
            $element->getAttributes()
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInAddAttribute()
    {
        $element = new Element('asdf');
        $element->addAttribute('    ', 'asdf');
    }

    public function testChildren()
    {
        $element = new Element('asdf');
        $this->assertEmpty($element->getChildren());
        $newElement = new Element('asdfasdf');
        $element->addChild($newElement);
        $this->assertEquals(
            array($newElement),
            $element->getChildren()
        );
        $this->assertTrue($element->removeChild($newElement));
        $this->assertFalse($element->removeChild($newElement));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInSetName()
    {
        $element = new Element('asdf');
        $element->setName(5);
    }
}
