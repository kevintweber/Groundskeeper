<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Del;
use PHPUnit\Framework\TestCase;

class DelTest extends TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $d = new Del($configuration, 0, 0, 'del');
        $this->assertTrue($d->isTransparentElement());
    }
}
