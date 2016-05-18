<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tests\TestableLogger;
use Groundskeeper\Tokens\Element;
use Psr\Log\NullLogger;

class ElementTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $configuration = new Configuration();
        $element = new Element($configuration, 0, 0, 'asdf');
        $this->assertEmpty($element->getAttributes());
        $this->assertEmpty($element->getChildren());
        $this->assertEquals('asdf', $element->getName());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInConstructor()
    {
        $configuration = new Configuration();
        $element = new Element($configuration, 0, 0, 5);
    }

    public function testAttributes()
    {
        $configuration = new Configuration();
        $element = new Element($configuration, 0, 0, 'asdf');
        $this->assertFalse($element->hasAttribute('class'));
        $this->assertEmpty($element->getAttributes());
        $element->addAttribute('class', 'c-asdf');
        $this->assertTrue($element->hasAttribute('class'));
        $this->assertEquals('c-asdf', $element->getAttribute('class'));
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
    public function testExceptionInGetAttribute()
    {
        $configuration = new Configuration();
        $element = new Element($configuration, 0, 0, 'asdf');
        $element->getAttribute('qwerty');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInAddAttribute()
    {
        $configuration = new Configuration();
        $element = new Element($configuration, 0, 0, 'asdf');
        $element->addAttribute('    ', 'asdf');
    }

    public function testChildren()
    {
        $configuration = new Configuration();
        $element = new Element($configuration, 0, 0, 'asdf');
        $this->assertEmpty($element->getChildren());
        $newElement = new Element($configuration, 0, 0, 'asdfasdf');
        $anotherElement = new Element($configuration, 0, 0, 'qwerty');
        $this->assertFalse($element->hasChild($newElement));
        $element->appendChild($newElement);
        $this->assertEquals(
            array($newElement),
            $element->getChildren()
        );
        $this->assertTrue($element->hasChild($newElement));
        $element->prependChild($anotherElement);
        $this->assertEquals(
            array($anotherElement, $newElement),
            $element->getChildren()
        );
        $this->assertTrue($element->removeChild($newElement));
        $this->assertFalse($element->hasChild($newElement));
        $this->assertFalse($element->removeChild($newElement));
        $this->assertFalse($element->hasChild($newElement));
    }

    public function testCleanWithNoCleanStategy()
    {
        $testableLogger = new TestableLogger();
        $configuration = new Configuration(array(
            'clean-strategy' => 'none'
        ));
        $element = new Element(
            $configuration,
            0,
            0,
            'asdf',
            array(
                'class' => 'asdf',
                'qwerty' => true
            )
        );
        $element->clean($testableLogger);
        $this->assertTrue($element->hasAttribute('qwerty'));
    }

    /**
     * @dataProvider cleanRemoveNonStandardAttributesDataProvider
     */
    public function testCleanRemoveNonStandardAttributes(array $attributes, $removedName)
    {
        $testableLogger = new TestableLogger();
        $configuration = new Configuration();
        $element = new Element(
            $configuration,
            0,
            0,
            'asdf',
            $attributes
        );
        $this->assertTrue($element->hasAttribute($removedName));
        $this->assertTrue($element->hasAttribute('id'));
        $element->clean($testableLogger);
        $this->assertFalse($element->hasAttribute($removedName));
        $this->assertTrue($element->hasAttribute('id'));
    }

    public function cleanRemoveNonStandardAttributesDataProvider()
    {
        return array(
            'simple' => array(
                array(
                    'id' => 'asdf1',
                    'data-asdf' => 'asdf2',
                    'asdf' => 'asdf3',
                    'style' => 'asdf4'
                ),
                'asdf'
            ),
            'bad aria attribute' => array(
                array(
                    'id' => 'asdf1',
                    'data-asdf' => 'asdf2',
                    'aria-rolling' => 'asdf3',
                    'style' => 'asdf4'
                ),
                'aria-rolling'
            )
        );
    }
}
