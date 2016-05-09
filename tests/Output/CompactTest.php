<?php

namespace Groundskeeper\Tests\Output;

use Groundskeeper\Configuration;
use Groundskeeper\Output\Compact;
use Groundskeeper\Tokens\Tokenizer;

class CompactTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider compactDataProvider
     */
    public function testCompact($html, $expectedOutput)
    {
        $configuration = new Configuration(array(
            'output' => 'compact'
        ));

        // Tokenize
        $tokenizer = new Tokenizer($configuration);
        $tokenContainer = $tokenizer->tokenize($html);

        // Output
        $compact = new Compact();
        $this->assertEquals(
            $expectedOutput,
            $compact($tokenContainer)
        );
    }

    public function compactDataProvider()
    {
        return array(
            'no transformation' => array(
                '<html><head><meta charset="UTF-8"/></head></html>',
                '<html><head><meta charset="UTF-8"/></head></html>'
            ),
            'transformation' => array(
                "
<html>
    <head>
        <meta charset='UTF-8' />
    </head>
</html>",
                '<html><head><meta charset="UTF-8"/></head></html>'
            ),
            'transformation with text' => array(
                "
<html>
    <body>
    Just some text.
    </body>
</html>",
                '<html><body> Just some text. </body></html>'
            )
        );
    }
}
