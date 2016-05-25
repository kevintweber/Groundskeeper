<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Label;

class LabelTest extends \PHPUnit_Framework_TestCase
{
    public function testIsTransparent()
    {
        $configuration = new Configuration();
        $l = new Label($configuration, 0, 0, 'label');
        $this->assertTrue($l->isInteractiveContent());
    }
}
