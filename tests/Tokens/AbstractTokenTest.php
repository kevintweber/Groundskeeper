<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;

class AbstractTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $abstractTokenMock = $this->getMockForAbstractClass(
            'Groundskeeper\\Tokens\\AbstractToken',
            array('comment', null)
        );
        $this->assertNull($abstractTokenMock->getIsValid());
        $this->assertNull($abstractTokenMock->getParent());
        $this->assertEquals('comment', $abstractTokenMock->getType());
        $this->assertEquals(0, $abstractTokenMock->getDepth());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInConstructor()
    {
        $abstractTokenMock = $this->getMockForAbstractClass(
            'Groundskeeper\\Tokens\\AbstractToken',
            array('asdf', null)
        );
    }

    public function testIsValid()
    {
        $abstractTokenMock = $this->getMockForAbstractClass(
            'Groundskeeper\\Tokens\\AbstractToken',
            array('comment', null)
        );
        $this->assertNull($abstractTokenMock->getIsValid());
        $abstractTokenMock->setIsValid(true);
        $this->assertTrue($abstractTokenMock->getIsValid());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInSetParent()
    {
        $abstractTokenMock = $this->getMockForAbstractClass(
            'Groundskeeper\\Tokens\\AbstractToken',
            array('comment', null)
        );
        $abstractTokenMock->setParent('asdf');
    }

    public function testValidate()
    {
        $configuration = new Configuration();
        $abstractTokenMock = $this->getMockForAbstractClass(
            'Groundskeeper\\Tokens\\AbstractToken',
            array('comment', null)
        );
        $abstractTokenMock->validate($configuration);
        $this->assertFalse($abstractTokenMock->getIsValid());
        $abstractTokenMock->validate($configuration);
    }
}
