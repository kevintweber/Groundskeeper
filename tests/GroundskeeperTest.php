<?php

namespace Groundskeeper\Tests;

use Groundskeeper\Groundskeeper;
use Psr\Log\NullLogger;

class GroundskeeperTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $groundskeeper = new Groundskeeper();
        $groundskeeper->setLogger(new NullLogger());
        $configuration = $groundskeeper->getConfiguration();
        $this->assertEquals(0, $configuration->get('indent-spaces'));
        $this->assertEquals('compact', $configuration->get('output'));

        $secondGroundskeeper = new Groundskeeper($configuration);
        $this->assertEquals($configuration, $secondGroundskeeper->getConfiguration());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInConstructor()
    {
        $groundskeeper = new Groundskeeper(5);
    }

    /**
     * @dataProvider cleanDataProvider
     */
    public function testClean($html, $expectedNoneOutput, $expectedLenientOutput, $expectedStandardOutput, $expectedAggressiveOutput)
    {
        // None test
        $noneGroundskeeper = new Groundskeeper(array(
            'clean-strategy' => 'none',
            'type-blacklist' => ''
        ));
        $noneGroundskeeper->setLogger(new NullLogger());
        $this->assertEquals(
            $expectedNoneOutput,
            $noneGroundskeeper->clean($html)
        );

        // Lenient test
        $lenientGroundskeeper = new Groundskeeper(array(
            'clean-strategy' => 'lenient',
            'type-blacklist' => ''
        ));
        $lenientGroundskeeper->setLogger(new NullLogger());
        $this->assertEquals(
            $expectedLenientOutput,
            $lenientGroundskeeper->clean($html)
        );

        // Standard test
        $standardGroundskeeper = new Groundskeeper(array(
            'clean-strategy' => 'standard',
            'type-blacklist' => ''
        ));
        $standardGroundskeeper->setLogger(new NullLogger());
        $this->assertEquals(
            $expectedStandardOutput,
            $standardGroundskeeper->clean($html)
        );

        // Aggressive test
        $aggressiveGroundskeeper = new Groundskeeper(array(
            'clean-strategy' => 'aggressive',
            'type-blacklist' => ''
        ));
        $aggressiveGroundskeeper->setLogger(new NullLogger());
        $this->assertEquals(
            $expectedAggressiveOutput,
            $aggressiveGroundskeeper->clean($html)
        );
    }

    public function cleanDataProvider()
    {
        return array(
            'cdata only' => array(
                '<![CDATA[asdf]]>',
                '<![CDATA[asdf]]>',
                '<![CDATA[asdf]]>',
                '<![CDATA[asdf]]>',
                '<![CDATA[asdf]]>'
            ),
            'cdata with whitespace' => array(
                '     <![CDATA[asdf]]>      ',
                '<![CDATA[asdf]]>',
                '<![CDATA[asdf]]>',
                '<![CDATA[asdf]]>',
                '<![CDATA[asdf]]>'
            ),
            'comment only' => array(
                '<!-- asdf -->',
                '<!-- asdf -->',
                '<!-- asdf -->',
                '<!-- asdf -->',
                '<!-- asdf -->'
            ),
            'comment with whitespace' => array(
                '     <!-- asdf -->      ',
                '<!-- asdf -->',
                '<!-- asdf -->',
                '<!-- asdf -->',
                '<!-- asdf -->'
            ),
            'doctype only' => array(
                '<!DOCTYPE asdf>',
                '<!DOCTYPE asdf>',
                '<!DOCTYPE asdf>',
                '<!DOCTYPE asdf>',
                '<!DOCTYPE asdf>'
            ),
            'doctype with whitespace' => array(
                '     <!DOCTYPE asdf>      ',
                '<!DOCTYPE asdf>',
                '<!DOCTYPE asdf>',
                '<!DOCTYPE asdf>',
                '<!DOCTYPE asdf>'
            ),
            'element only' => array(
                '<asdf/>',
                '<asdf/>',
                '<asdf/>',
                '<asdf/>',
                '<asdf/>'
            ),
            'element with whitespace' => array(
                '     <asdf/>      ',
                '<asdf/>',
                '<asdf/>',
                '<asdf/>',
                '<asdf/>'
            ),
            'text only' => array(
                'asdf',
                'asdf',
                'asdf',
                'asdf',
                'asdf'
            ),
            'text with whitespace' => array(
                '     asdf      ',
                'asdf',
                'asdf',
                'asdf',
                'asdf'
            ),
            'element with child' => array(
                '<asdf1><asdf2/></asdf1>',
                '<asdf1><asdf2/></asdf1>',
                '<asdf1><asdf2/></asdf1>',
                '<asdf1><asdf2/></asdf1>',
                '<asdf1><asdf2/></asdf1>'
            ),
            'element with child and whitespace' => array(
                '    <asdf1>           <asdf2/>          </asdf1>           ',
                '<asdf1><asdf2/></asdf1>',
                '<asdf1><asdf2/></asdf1>',
                '<asdf1><asdf2/></asdf1>',
                '<asdf1><asdf2/></asdf1>'
            ),
            'element with child and attributes' => array(
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>'
            ),
            'element with child and valid and invalid attributes' => array(
                '<asdf1 id="asdf3"   asdf5="asdf6">Text goes here<asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3" asdf5="asdf6">Text goes here<asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3" asdf5="asdf6">Text goes here<asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3">Text goes here<asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3">Text goes here<asdf2 class="asdf4"/></asdf1>'
            ),
            'element with case insensitive attribute values' => array(
                '<asdf role=ASDF1>Text goes here</asdf>',
                '<asdf role="ASDF1">Text goes here</asdf>',
                '<asdf role="asdf1">Text goes here</asdf>',
                '<asdf role="asdf1">Text goes here</asdf>',
                '<asdf role="asdf1">Text goes here</asdf>'
            ),
        );
    }

    /**
     * @dataProvider cleanWithElementRemovalDataProvider
     */
    public function testCleanWithElementRemoval($removedElements, $html, $expectedOutput)
    {
        $groundskeeper = new Groundskeeper(array(
            'element-blacklist' => $removedElements
        ));
        $groundskeeper->setLogger(new NullLogger());
        $this->assertEquals(
            $expectedOutput,
            $groundskeeper->clean($html)
        );
    }

    public function cleanWithElementRemovalDataProvider()
    {
        return array(
            'none' => array(
                '',
                '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>',
                '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>'
            ),
            'single closed element' => array(
                'br',
                '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>',
                '<div class="asdf1">asdf2<i>asdf3</i><em>asdf4</em>asdf5</div>'
            /* ), */
            /* 'single open element' => array( */
            /*     'em', */
            /*     '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>', */
            /*     '<div class="asdf1">asdf2<i>asdf3</i><br/>asdf5</div>' */
            /* ), */
            /* 'multiple elements' => array( */
            /*     'em,br', */
            /*     '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>', */
            /*     '<div class="asdf1">asdf2<i>asdf3</i>asdf5</div>' */
            /* ), */
            /* 'parent element' => array( */
            /*     'em,br,div', */
            /*     '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>', */
            /*     '' */
            )
        );
    }

    /**
     * @dataProvider cleanWithErrorFixDataProvider
     */
    /* public function testCleanWithErrorFix($html, $expectedOutput) */
    /* { */
    /*     $groundskeeper = new Groundskeeper(array( */
    /*         'error-strategy' => 'fix', */
    /*         'type-blacklist' => '' */
    /*     )); */
    /*     $groundskeeper->setLogger(new NullLogger()); */
    /*     $this->assertEquals( */
    /*         $expectedOutput, */
    /*         $groundskeeper->clean($html) */
    /*     ); */
    /* } */

    public function cleanWithErrorFixDataProvider()
    {
        return array(
            'no fixes' => array(
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>'
            ),
            'html - bad child token' => array(
                '<html><!-- asdf --><![CDATA[asdf]]><head><title>Asdf1</title></head>asdf<body>Yo!</body></html>',
                '<html><!-- asdf --><head><title>Asdf1</title></head><body>Yo!</body></html>'
            ),
            'html - missing head' => array(
                '<html><body>Yo!</body></html>',
                '<html><head></head><body>Yo!</body></html>'
            ),
            'html - missing body' => array(
                '<html><head><title>Asdf1</title></head></html>',
                '<html><head><title>Asdf1</title></head><body></body></html>'
            ),
            'html - multiple heads' => array(
                '<html><head><title>Asdf1</title></head><head><title>Asdf2</title></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>'
            ),
            'html - multiple bodies' => array(
                '<html><head><title>Asdf1</title></head><body>Yo!</body><body>Yo!Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>'
            ),
            'html - body before head' => array(
                '<html><body>Yo!</body><head><title>Asdf1</title></head></html>',
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>'
            ),
            'head - extra elements' => array(
                '<html><head><title>Asdf1</title><p>Asdf2</p></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>'
            ),
            'head - no title' => array(
                '<html><head></head><body>Yo!</body></html>',
                '<html><head><title></title></head><body>Yo!</body></html>'
            ),
            'head - multiple titles' => array(
                '<html><head><title>Asdf1</title><title>Asdf2</title></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>'
            ),
            'head - multiple base' => array(
                '<html><head><base href="asdf" /><title>Asdf1</title><base href="asdf" /></head><body>Yo!</body></html>',
                '<html><head><base href="asdf"/><title>Asdf1</title></head><body>Yo!</body></html>'
            ),
            'title contains comment' => array(
                '<html><head><title>Asd<!-- just a comment -->f1</title></head><body>Yo!</body></html>',
                '<html><head><title>Asd<!-- just a comment -->f1</title></head><body>Yo!</body></html>'
            ),
            'title contains markup' => array(
                '<html><head><title>Asd<b>f1</b></title></head><body>Yo!</body></html>',
                '<html><head><title>Asd</title></head><body>Yo!</body></html>'
            )
        );
    }
}
