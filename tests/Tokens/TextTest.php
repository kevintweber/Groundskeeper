<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Text;

class TextTest extends \PHPUnit_Framework_TestCase
{
    public function testText()
    {
        $text = new Text(null, 'asdf');
        $this->assertEquals('asdf', $text->getValue());
        $text->setIsValid(true);
        $configuration = new Configuration();
        $this->assertEquals(
            'asdf',
            $text->toString($configuration)
        );

        $text->setIsValid(false);
        $this->assertEquals(
            '',
            $text->toString($configuration)
        );
    }
}
