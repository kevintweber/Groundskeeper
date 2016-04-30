<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
    public function testText()
    {
        $configuration = new Configuration(array(
            'type-blacklist' => ''
        ));
        $text = new Text($configuration, 'asdf');
        $this->assertEquals('asdf', $text->getValue());
        $this->assertEquals(
            'asdf',
            $text->toHtml('', '')
        );
    }
}
