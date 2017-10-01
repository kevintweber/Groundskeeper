<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\CData;
use PHPUnit\Framework\TestCase;

class CDataTest extends TestCase
{
    public function testCData()
    {
        $configuration = new Configuration(array(
            'type-blacklist' => ''
        ));
        $cdata = new CData($configuration, 0, 0, 'asdf');
        $this->assertEquals('asdf', $cdata->getValue());
        $this->assertEquals(
            '<![CDATA[asdf]]>',
            $cdata->toHtml('', '')
        );
    }
}
