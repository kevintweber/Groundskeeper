<?php

namespace Groundskeeper\Tests\Tokens\ElementTypes;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Hr;
use PHPUnit\Framework\TestCase;

class ClosedElementTest extends TestCase
{
    public function testClosedElement()
    {
        $configuration = new Configuration();
        $hr = new Hr($configuration, 0, 0, 'hr');
        $hr->appendChild($hr);
        $hr->prependChild($hr);
        $this->assertEmpty($hr->getChildren());
    }
}
