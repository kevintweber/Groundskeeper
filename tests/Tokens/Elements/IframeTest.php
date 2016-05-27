<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Iframe;

class IframeTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInteractiveContent()
    {
        $configuration = new Configuration();
        $d = new Iframe($configuration, 0, 0, 'iframe');
        $this->assertTrue($d->isInteractiveContent());
    }
}
