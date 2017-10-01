<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Details;
use PHPUnit\Framework\TestCase;

class DetailsTest extends TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $d = new Details($configuration, 0, 0, 'details');
        $this->assertTrue($d->isInteractiveContent());
    }
}
