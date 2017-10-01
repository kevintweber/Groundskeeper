<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Text;
use PHPUnit\Framework\TestCase;

class TextTest extends TestCase
{
    public function testText()
    {
        $configuration = new Configuration(array(
            'type-blacklist' => ''
        ));
        $text = new Text($configuration, 0, 0, 'asdf');
        $this->assertEquals('asdf', $text->getValue());
        $this->assertEquals(
            'asdf',
            $text->toHtml('', '')
        );
    }
}
