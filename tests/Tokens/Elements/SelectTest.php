<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Select;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $s = new Select($configuration, 0, 0, 'select');
        $this->assertTrue($s->isInteractiveContent());
    }
}
