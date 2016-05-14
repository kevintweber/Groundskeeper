<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;

class AbstractTokenTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $abstractTokenMock = $this->createAbstractTokenMock();
        $this->assertNull($abstractTokenMock->getParent());
        $this->assertEquals(0, $abstractTokenMock->getDepth());
    }

    protected function createAbstractTokenMock()
    {
        $configuration = new Configuration();

        return $this->getMockForAbstractClass(
            'Groundskeeper\\Tokens\\AbstractToken',
            array($configuration, null)
        );
    }
}
