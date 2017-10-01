<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Iframe;
use PHPUnit\Framework\TestCase;

class IframeTest extends TestCase
{
    public function testIsInteractiveContent()
    {
        $configuration = new Configuration();
        $d = new Iframe($configuration, 0, 0, 'iframe');
        $this->assertTrue($d->isInteractiveContent());
    }
}
