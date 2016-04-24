<?php

namespace Groundskeeper\Tests\Output;

use Groundskeeper\Configuration;
use Groundskeeper\Output\Pretty;
use Groundskeeper\Tokens\Tokenizer;

class PrettyTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider prettyDataProvider
     */
    public function testPretty($html, $expectedOutput)
    {
        $configuration = new Configuration(array(
            'output' => 'pretty'
        ));

        // Tokenize
        $tokenizer = new Tokenizer($configuration);
        $tokens = $tokenizer->tokenize($html);

        // Clean
        foreach ($tokens as $token) {
            $token->validate($configuration);
        }

        $pretty = new Pretty($configuration);
        $this->assertEquals(
            $expectedOutput,
            $pretty->printTokens($tokens)
        );
    }

    public function prettyDataProvider()
    {
        return array(
            'transformation' => array(
                '<html><head><meta charset="UTF-8"/></head></html>',
                "<html>
    <head>
        <meta charset=\"UTF-8\"/>
    </head>
</html>"
            ),
            'whitespace transformation' => array(
                "
<html>
    <head>
        <meta charset='UTF-8' />
    </head>
</html>",
                "<html>
    <head>
        <meta charset=\"UTF-8\"/>
    </head>
</html>"
            ),
            'transformation with text' => array(
                "
<html>
    <body>Just some text.</body>
</html>",
                "<html>
    <body>
        Just some text.
    </body>
</html>"
            )
        );
    }
}
