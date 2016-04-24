<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\CData;

class CDataTest extends \PHPUnit_Framework_TestCase
{
    public function testCData()
    {
        $cdata = new CData(null, 'asdf');
        $this->assertEquals('asdf', $cdata->getValue());
        $cdata->setIsValid(true);
        $configuration = new Configuration();
        $this->assertEquals(
            '<![CDATA[asdf]]>',
            $cdata->toString($configuration)
        );

        $cdata->setIsValid(false);
        $this->assertEquals(
            '',
            $cdata->toString($configuration)
        );
    }
}
