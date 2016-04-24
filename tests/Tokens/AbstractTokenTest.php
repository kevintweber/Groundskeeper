<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;

class AbstractTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $abstractTokenMock = $this->createAbstractTokenMock('comment');
        $this->assertNull($abstractTokenMock->getParent());
        $this->assertEquals('comment', $abstractTokenMock->getType());
        $this->assertEquals(0, $abstractTokenMock->getDepth());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInConstructor()
    {
        $abstractTokenMock = $this->createAbstractTokenMock('asdf');
    }

    protected function createAbstractTokenMock($type = 'comment')
    {
        $configuration = new Configuration();

        return $this->getMockForAbstractClass(
            'Groundskeeper\\Tokens\\AbstractToken',
            array($type, $configuration, null)
        );
    }
}
