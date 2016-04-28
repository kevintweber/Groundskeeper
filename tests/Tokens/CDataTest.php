<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\CData;

class CDataTest extends \PHPUnit_Framework_TestCase
{
    public function testCData()
    {
        $configuration = new Configuration(array(
            'type-blacklist' => 'none'
        ));
        $cdata = new CData($configuration, null, 'asdf');
        $this->assertEquals('asdf', $cdata->getValue());
        $this->assertEquals(
            '<![CDATA[asdf]]>',
            $cdata->toHtml('', '')
        );
    }
}
