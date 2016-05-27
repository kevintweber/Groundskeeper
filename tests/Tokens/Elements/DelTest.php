<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Del;

class DelTest extends \PHPUnit_Framework_TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $d = new Del($configuration, 0, 0, 'del');
        $this->assertTrue($d->isTransparentElement());
    }
}
