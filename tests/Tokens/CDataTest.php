<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\CData;

class CDataTest extends \PHPUnit_Framework_TestCase
{
    public function testCData()
    {
        $configuration = new Configuration(array(
            'remove-types' => 'none'
        ));
        $cdata = new CData($configuration, null, 'asdf');
        $this->assertEquals('asdf', $cdata->getValue());
        $this->assertEquals(
            '<![CDATA[asdf]]>',
            $cdata->toHtml('', '')
        );
    }

    public function testCDataIsRemovedType()
    {
        $configuration = new Configuration(array(
            'remove-types' => 'cdata'
        ));
        $cdata = new CData($configuration, null, 'asdf');
        $this->assertEquals(
            '',
            $cdata->toHtml('', '')
        );
    }
}
