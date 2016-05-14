<?php

namespace Groundskeeper\Tests\Tokens\ElementTypes;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Hr;

class ClosedElementTest extends \PHPUnit_Framework_TestCase
{
    public function testClosedElement()
    {
        $configuration = new Configuration();
        $hr = new Hr($configuration, 'hr');
        $hr->appendChild($hr);
        $hr->prependChild($hr);
        $this->assertEmpty($hr->getChildren());
    }
}
