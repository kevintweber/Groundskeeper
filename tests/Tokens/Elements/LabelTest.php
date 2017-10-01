<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Label;
use PHPUnit\Framework\TestCase;

class LabelTest extends TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $l = new Label($configuration, 0, 0, 'label');
        $this->assertTrue($l->isInteractiveContent());
    }
}
