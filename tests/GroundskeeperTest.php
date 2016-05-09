<?php

namespace Groundskeeper\Tests;

use Groundskeeper\Groundskeeper;
use Psr\Log\NullLogger;

class GroundskeeperTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $logger = new TestableLogger();
        $groundskeeper = new Groundskeeper();
        $groundskeeper->setLogger($logger);
        $configuration = $groundskeeper->getConfiguration();
        $this->assertEquals(0, $configuration->get('indent-spaces'));
        $this->assertEquals('compact', $configuration->get('output'));

        $secondGroundskeeper = new Groundskeeper($configuration);
        $this->assertEquals($configuration, $secondGroundskeeper->getConfiguration());
        $this->assertEmpty($logger->getLogs());
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
    public function testClean($html,
                              $expectedNoneOutput,
                              $expectedNoneLogCount,
                              $expectedLenientOutput,
                              $expectedLenientLogCount,
                              $expectedStandardOutput,
                              $expectedStandardLogCount,
                              $expectedAggressiveOutput,
                              $expectedAggressiveLogCount,
                              $debugOutputLogFor = null)
    {
        // None test
        $noneGroundskeeper = new Groundskeeper(array(
            'clean-strategy' => 'none',
            'type-blacklist' => ''
        ));
        $noneLogger = new TestableLogger();
        $noneGroundskeeper->setLogger($noneLogger);
        $noneResult = $noneGroundskeeper->clean($html);
        if ($debugOutputLogFor == 'none') {
            var_dump($noneLogger->getLogs());
        }

        $this->assertEquals(
            $expectedNoneOutput,
            $noneResult,
            'Clean strategy - none'
        );
        $this->assertEquals(
            $expectedNoneLogCount,
            count($noneLogger->getLogs()),
            'Log counter: Clean strategy - none'
        );

        // Lenient test
        $lenientGroundskeeper = new Groundskeeper(array(
            'clean-strategy' => 'lenient',
            'type-blacklist' => ''
        ));
        $lenientLogger = new TestableLogger();
        $lenientGroundskeeper->setLogger($lenientLogger);
        $lenientResult = $lenientGroundskeeper->clean($html);
        if ($debugOutputLogFor == 'lenient') {
            var_dump($lenientLogger->getLogs());
        }

        $this->assertEquals(
            $expectedLenientOutput,
            $lenientResult,
            'Clean strategy - lenient'
        );
        $this->assertEquals(
            $expectedLenientLogCount,
            count($lenientLogger->getLogs()),
            'Log counter: Clean strategy - lenient'
        );

        // Standard test
        $standardGroundskeeper = new Groundskeeper(array(
            'clean-strategy' => 'standard',
            'type-blacklist' => ''
        ));
        $standardLogger = new TestableLogger();
        $standardGroundskeeper->setLogger($standardLogger);
        $standardResult = $standardGroundskeeper->clean($html);
        if ($debugOutputLogFor == 'standard') {
            var_dump($standardLogger->getLogs());
        }

        $this->assertEquals(
            $expectedStandardOutput,
            $standardResult,
            'Clean strategy - standard'
        );
        $this->assertEquals(
            $expectedStandardLogCount,
            count($standardLogger->getLogs()),
            'Log counter: Clean strategy - standard'
        );

        // Aggressive test
        $aggressiveGroundskeeper = new Groundskeeper(array(
            'clean-strategy' => 'aggressive',
            'type-blacklist' => ''
        ));
        $aggressiveLogger = new TestableLogger();
        $aggressiveGroundskeeper->setLogger($aggressiveLogger);
        $aggressiveResults = $aggressiveGroundskeeper->clean($html);
        if ($debugOutputLogFor == 'aggressive') {
            var_dump($aggressiveLogger->getLogs());
        }

        $this->assertEquals(
            $expectedAggressiveOutput,
            $aggressiveResults,
            'Clean strategy - aggressive'
        );
        $this->assertEquals(
            $expectedAggressiveLogCount,
            count($aggressiveLogger->getLogs()),
            'Log counter: Clean strategy - aggressive'
        );
    }

    public function cleanDataProvider()
    {
        return array(
            'cdata only' => array(
                '<![CDATA[asdf]]>',
                '<![CDATA[asdf]]>',
                0,
                '<![CDATA[asdf]]>',
                0,
                '<![CDATA[asdf]]>',
                0,
                '<![CDATA[asdf]]>',
                0
            ),
            'cdata with whitespace' => array(
                '     <![CDATA[asdf]]>      ',
                '<![CDATA[asdf]]>',
                0,
                '<![CDATA[asdf]]>',
                0,
                '<![CDATA[asdf]]>',
                0,
                '<![CDATA[asdf]]>',
                0
            ),
            'comment only' => array(
                '<!-- asdf -->',
                '<!-- asdf -->',
                0,
                '<!-- asdf -->',
                0,
                '<!-- asdf -->',
                0,
                '<!-- asdf -->',
                0
            ),
            'comment with whitespace' => array(
                '     <!-- asdf -->      ',
                '<!-- asdf -->',
                0,
                '<!-- asdf -->',
                0,
                '<!-- asdf -->',
                0,
                '<!-- asdf -->',
                0
            ),
            'doctype only' => array(
                '<!DOCTYPE asdf>',
                '<!DOCTYPE asdf>',
                0,
                '<!DOCTYPE asdf>',
                0,
                '<!DOCTYPE asdf>',
                0,
                '<!DOCTYPE asdf>',
                0
            ),
            'doctype with whitespace' => array(
                '     <!DOCTYPE asdf>      ',
                '<!DOCTYPE asdf>',
                0,
                '<!DOCTYPE asdf>',
                0,
                '<!DOCTYPE asdf>',
                0,
                '<!DOCTYPE asdf>',
                0
            ),
            'doctype - with parent' => array(
                '<div><!DOCTYPE asdf1>asdf2</div>',
                '<div><!DOCTYPE asdf1>asdf2</div>',
                0,
                '<div><!DOCTYPE asdf1>asdf2</div>',
                1,
                '<div>asdf2</div>',
                2,
                '<div>asdf2</div>',
                2
            ),
            'element only' => array(
                '<asdf/>',
                '<asdf/>',
                0,
                '<asdf/>',
                0,
                '<asdf/>',
                0,
                '<asdf/>',
                0
            ),
            'element with whitespace' => array(
                '     <asdf/>      ',
                '<asdf/>',
                0,
                '<asdf/>',
                0,
                '<asdf/>',
                0,
                '<asdf/>',
                0
            ),
            'text only' => array(
                'asdf',
                'asdf',
                0,
                'asdf',
                0,
                'asdf',
                0,
                'asdf',
                0
            ),
            'text with whitespace' => array(
                '     asdf      ',
                'asdf',
                0,
                'asdf',
                0,
                'asdf',
                0,
                'asdf',
                0
            ),
            'element with child' => array(
                '<asdf1><asdf2/></asdf1>',
                '<asdf1><asdf2/></asdf1>',
                0,
                '<asdf1><asdf2/></asdf1>',
                0,
                '<asdf1><asdf2/></asdf1>',
                0,
                '<asdf1><asdf2/></asdf1>',
                0
            ),
            'element with child and whitespace' => array(
                '    <asdf1>           <asdf2/>          </asdf1>           ',
                '<asdf1><asdf2/></asdf1>',
                0,
                '<asdf1><asdf2/></asdf1>',
                0,
                '<asdf1><asdf2/></asdf1>',
                0,
                '<asdf1><asdf2/></asdf1>',
                0
            ),
            'element with child and attributes' => array(
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>',
                0,
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>',
                0,
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>',
                0,
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>',
                0
            ),
            'element with child and valid and invalid attributes' => array(
                '<asdf1 id="asdf3"   asdf5="asdf6">Text goes here<asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3" asdf5="asdf6">Text goes here<asdf2 class="asdf4"/></asdf1>',
                0,
                '<asdf1 id="asdf3" asdf5="asdf6">Text goes here<asdf2 class="asdf4"/></asdf1>',
                0,
                '<asdf1 id="asdf3">Text goes here<asdf2 class="asdf4"/></asdf1>',
                1,
                '<asdf1 id="asdf3">Text goes here<asdf2 class="asdf4"/></asdf1>',
                1
            ),
            'element with case insensitive attribute values' => array(
                '<asdf role=ASDF1>Text goes here</asdf>',
                '<asdf role="ASDF1">Text goes here</asdf>',
                0,
                '<asdf role="asdf1">Text goes here</asdf>',
                1,
                '<asdf role="asdf1">Text goes here</asdf>',
                1,
                '<asdf role="asdf1">Text goes here</asdf>',
                1
            ),
            'a - correct usage' => array(
                '<a href="www.example.com">Example.com</a>',
                '<a href="www.example.com">Example.com</a>',
                0,
                '<a href="www.example.com">Example.com</a>',
                0,
                '<a href="www.example.com">Example.com</a>',
                0,
                '<a href="www.example.com">Example.com</a>',
                0
            ),
            'a - itemprop requires href' => array(
                '<a itemprop="yo">Example.com</a>',
                '<a itemprop="yo">Example.com</a>',
                0,
                '<a itemprop="yo" href="">Example.com</a>',
                1,
                '<a itemprop="yo" href="">Example.com</a>',
                1,
                '<a itemprop="yo" href="">Example.com</a>',
                1
            ),
            'a - with comment and illegal attribute' => array(
                '<a href="www.example.com" with="stuff"><!-- comment -->Example.com</a>',
                '<a href="www.example.com" with="stuff"><!-- comment -->Example.com</a>',
                0,
                '<a href="www.example.com" with="stuff"><!-- comment -->Example.com</a>',
                0,
                '<a href="www.example.com"><!-- comment -->Example.com</a>',
                1,
                '<a href="www.example.com"><!-- comment -->Example.com</a>',
                1
            ),
            'a - with child a' => array(
                '<a href="www.example.com">Example.com<a href="www.example.org">Example.org</a></a>',
                '<a href="www.example.com">Example.com<a href="www.example.org">Example.org</a></a>',
                0,
                '<a href="www.example.com">Example.com<a href="www.example.org">Example.org</a></a>',
                0,
                '<a href="www.example.com">Example.com</a>',
                1,
                '<a href="www.example.com">Example.com</a>',
                1
            ),
            'a - without href' => array(
                '<a title="www.example.com" target="_blank">Example.com</a>',
                '<a title="www.example.com" target="_blank">Example.com</a>',
                0,
                '<a title="www.example.com" target="_blank">Example.com</a>',
                0,
                '<a title="www.example.com">Example.com</a>',
                1,
                '<a title="www.example.com">Example.com</a>',
                1
            ),
            'address - heading content child' => array(
                '<body><address>asdf1<br/>asdf3<h1>asdf2</h1></address></body>',
                '<body><address>asdf1<br/>asdf3<h1>asdf2</h1></address></body>',
                0,
                '<body><address>asdf1<br/>asdf3<h1>asdf2</h1></address></body>',
                0,
                '<body><address>asdf1<br/>asdf3</address></body>',
                1,
                '<body><address>asdf1<br/>asdf3</address></body>',
                1
            ),
            'address - address child' => array(
                '<body><address>asdf1<address>asdf2</address></address></body>',
                '<body><address>asdf1<address>asdf2</address></address></body>',
                0,
                '<body><address>asdf1<address>asdf2</address></address></body>',
                0,
                '<body><address>asdf1</address></body>',
                1,
                '<body><address>asdf1</address></body>',
                1
            ),
            'address - sectioning content child' => array(
                '<body><address>asdf1<article>asdf2</article></address></body>',
                '<body><address>asdf1<article>asdf2</article></address></body>',
                0,
                '<body><address>asdf1<article>asdf2</article></address></body>',
                0,
                '<body><address>asdf1</address></body>',
                1,
                '<body><address>asdf1</address></body>',
                1
            ),
            'base - correct usage' => array(
                '<html><head><title>Asdf1</title><base href="www.example.com"/></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><base href="www.example.com"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><base href="www.example.com"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><base href="www.example.com"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><base href="www.example.com"/></head><body>Yo!</body></html>',
                0
            ),
            'base - href required' => array(
                '<html><head><title>Asdf1</title><base class="www.example.com"/></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><base class="www.example.com"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><base class="www.example.com"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2
            ),
            'base - must be child of head' => array(
                '<html><head><title>Asdf1</title></head><body>Yo!<base href="www.example.com"/></body></html>',
                '<html><head><title>Asdf1</title></head><body>Yo!<base href="www.example.com"/></body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!<base href="www.example.com"/></body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2
            ),
            'blockquote - correct usage' => array(
                '<div><blockquote id="yo" cite="www.example.com">Yo!</blockquote></div>',
                '<div><blockquote id="yo" cite="www.example.com">Yo!</blockquote></div>',
                0,
                '<div><blockquote id="yo" cite="www.example.com">Yo!</blockquote></div>',
                0,
                '<div><blockquote id="yo" cite="www.example.com">Yo!</blockquote></div>',
                0,
                '<div><blockquote id="yo" cite="www.example.com">Yo!</blockquote></div>',
                0
            ),
            'body - child of non-html element' => array(
                '<html><head><title>Asdf1</title></head><div><body id="yo">Yo!</body></div></html>',
                '<html><head><title>Asdf1</title></head><div><body id="yo">Yo!</body></div></html>',
                0,
                '<html><head><title>Asdf1</title></head><div><body id="yo">Yo!</body></div><body></body></html>',
                3,
                '<html><head><title>Asdf1</title></head><body></body></html>',
                2,
                '<html><head><title>Asdf1</title></head><body></body></html>',
                2
            ),
            'dl -correct usage' => array(
                '<dl><!-- comment --><dt>Authors</dt><dd>Kevin</dd><script type="text/javascript">console.log("asdf");</script></dl>',
                '<dl><!-- comment --><dt>Authors</dt><dd>Kevin</dd><script type="text/javascript">console.log("asdf");</script></dl>',
                0,
                '<dl><!-- comment --><dt>Authors</dt><dd>Kevin</dd><script type="text/javascript">console.log("asdf");</script></dl>',
                0,
                '<dl><!-- comment --><dt>Authors</dt><dd>Kevin</dd><script type="text/javascript">console.log("asdf");</script></dl>',
                0,
                '<dl><!-- comment --><dt>Authors</dt><dd>Kevin</dd><script type="text/javascript">console.log("asdf");</script></dl>',
                0
            ),
            'dl - contains text and wrong element' => array(
                '<dl><dt>Authors</dt><dd>Kevin</dd>Weber<p>Whoa!</p></dl>',
                '<dl><dt>Authors</dt><dd>Kevin</dd>Weber<p>Whoa!</p></dl>',
                0,
                '<dl><dt>Authors</dt><dd>Kevin</dd>Weber<p>Whoa!</p></dl>',
                0,
                '<dl><dt>Authors</dt><dd>Kevin</dd></dl>',
                2,
                '<dl><dt>Authors</dt><dd>Kevin</dd></dl>',
                2
            ),
            'dd & dt - wrong parent' => array(
                '<div>Whoa!<dd>asdf1</dd><dt>asdf2</dt></div>',
                '<div>Whoa!<dd>asdf1</dd><dt>asdf2</dt></div>',
                0,
                '<div>Whoa!<dd>asdf1</dd><dt>asdf2</dt></div>',
                0,
                '<div>Whoa!</div>',
                4,
                '<div>Whoa!</div>',
                4
            ),
            'dt - incorrect child elements' => array(
                '<dl><dt>asdf1<H2>asdf2</H2></dt><dt><![CDATA[ hmmm... ]]></dt></dl>',
                '<dl><dt>asdf1<h2>asdf2</h2></dt><dt><![CDATA[hmmm...]]></dt></dl>',
                0,
                '<dl><dt>asdf1<h2>asdf2</h2></dt><dt><![CDATA[hmmm...]]></dt></dl>',
                0,
                '<dl><dt>asdf1</dt><dt></dt></dl>',
                2,
                '<dl><dt>asdf1</dt><dt></dt></dl>',
                2
            ),
            'footer - bad child' => array(
                '<div><footer>asdf1<footer>asdf2</footer>asdf3</footer></div>',
                '<div><footer>asdf1<footer>asdf2</footer>asdf3</footer></div>',
                0,
                '<div><footer>asdf1<footer>asdf2</footer>asdf3</footer></div>',
                0,
                '<div><footer>asdf1asdf3</footer></div>',
                2,
                '<div><footer>asdf1asdf3</footer></div>',
                2
            ),
            'head - child of non-html element' => array(
                '<html><div><head><title>Asdf1</title></head></div><body>Yo!</body></html>',
                '<html><div><head><title>Asdf1</title></head></div><body>Yo!</body></html>',
                0,
                '<html><head><title></title></head><div><head><title>Asdf1</title></head></div><body>Yo!</body></html>',
                4,
                '<html><head><title></title></head><body>Yo!</body></html>',
                3,
                '<html><head><title></title></head><body>Yo!</body></html>',
                3
            ),
            'head - extra elements' => array(
                '<html><head><title>Asdf1</title><p>Asdf2</p></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><p>Asdf2</p></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><p>Asdf2</p></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'head - no title' => array(
                '<html><head></head><body>Yo!</body></html>',
                '<html><head></head><body>Yo!</body></html>',
                0,
                '<html><head><title></title></head><body>Yo!</body></html>',
                1,
                '<html><head><title></title></head><body>Yo!</body></html>',
                1,
                '<html><head><title></title></head><body>Yo!</body></html>',
                1
            ),
            'head - multiple titles' => array(
                '<html><head><title>Asdf1</title><title>Asdf2</title></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><title>Asdf2</title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><title>Asdf2</title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'head - multiple base' => array(
                '<html><head><base href="asdf" /><title>Asdf1</title><base href="asdf" /></head><body>Yo!</body></html>',
                '<html><head><base href="asdf"/><title>Asdf1</title><base href="asdf"/></head><body>Yo!</body></html>',
                0,
                '<html><head><base href="asdf"/><title>Asdf1</title><base href="asdf"/></head><body>Yo!</body></html>',
                0,
                '<html><head><base href="asdf"/><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><base href="asdf"/><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'head - contains comment' => array(
                '<html><head> <!-- Comment here! -->  <title>Asdf1</title></head><body>Yo!</body></html>',
                '<html><head><!-- Comment here! --><title>Asdf1</title></head><body>Yo!</body></html>',
                0,
                '<html><head><!-- Comment here! --><title>Asdf1</title></head><body>Yo!</body></html>',
                0,
                '<html><head><!-- Comment here! --><title>Asdf1</title></head><body>Yo!</body></html>',
                0,
                '<html><head><!-- Comment here! --><title>Asdf1</title></head><body>Yo!</body></html>',
                0
            ),
            'header - bad child' => array(
                '<div><footer>asdf1<header>asdf2</header>asdf3</footer></div>',
                '<div><footer>asdf1<header>asdf2</header>asdf3</footer></div>',
                0,
                '<div><footer>asdf1<header>asdf2</header>asdf3</footer></div>',
                0,
                '<div><footer>asdf1asdf3</footer></div>',
                2,
                '<div><footer>asdf1asdf3</footer></div>',
                2
            ),
            'html - correct usage' => array(
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                0
            ),
            'html - with attributes' => array(
                '<html class="js" oops><head><title>Asdf1</title></head><body>Yo!</body></html>',
                '<html class="js" oops><head><title>Asdf1</title></head><body>Yo!</body></html>',
                0,
                '<html class="js" oops><head><title>Asdf1</title></head><body>Yo!</body></html>',
                0,
                '<html class="js"><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html class="js"><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'html - must have no parent elements' => array(
                '<div><html><head><title>Asdf1</title></head><body>Yo!</body></html>asdf</div>',
                '<div><html><head><title>Asdf1</title></head><body>Yo!</body></html>asdf</div>',
                0,
                '<div><html><head><title>Asdf1</title></head><body>Yo!</body></html>asdf</div>',
                1,
                '<div>asdf</div>',
                2,
                '<div>asdf</div>',
                2
            ),
            'html - bad child tokens' => array(
                '<html><!-- asdf --><![CDATA[asdf]]><head><title>Asdf1</title></head>asdf<body>Yo!</body></html>',
                '<html><!-- asdf --><![CDATA[asdf]]><head><title>Asdf1</title></head>asdf<body>Yo!</body></html>',
                0,
                '<html><!-- asdf --><![CDATA[asdf]]><head><title>Asdf1</title></head>asdf<body>Yo!</body></html>',
                0,
                '<html><!-- asdf --><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2,
                '<html><!-- asdf --><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2
            ),
            'html - missing head' => array(
                '<html><body>Yo!</body></html>',
                '<html><body>Yo!</body></html>',
                0,
                '<html><head><title></title></head><body>Yo!</body></html>',
                2,
                '<html><head><title></title></head><body>Yo!</body></html>',
                2,
                '<html><head><title></title></head><body>Yo!</body></html>',
                2
            ),
            'html - missing body' => array(
                '<html><head><title>Asdf1</title></head></html>',
                '<html><head><title>Asdf1</title></head></html>',
                0,
                '<html><head><title>Asdf1</title></head><body></body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body></body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body></body></html>',
                1
            ),
            'html - multiple heads' => array(
                '<html><head><title>Asdf1</title></head><head><title>Asdf2</title></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><head><title>Asdf2</title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><head><title>Asdf2</title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'html - multiple bodies' => array(
                '<html><head><title>Asdf1</title></head><body>Yo!</body><body>Yo!Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body>Yo!</body><body>Yo!Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body><body>Yo!Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'html - body before head' => array(
                '<html><body>Yo!</body><head><title>Asdf1</title></head></html>',
                '<html><body>Yo!</body><head><title>Asdf1</title></head></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'link - correct usage in head' => array(
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help"/></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help"/></head><body>Yo!</body></html>',
                0
            ),
            'link - correct usage in body' => array(
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="stylesheet"/><link href="www.example.com" itemprop="stylesheet"/>Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="stylesheet"/><link href="www.example.com" itemprop="stylesheet"/>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="stylesheet"/><link href="www.example.com" itemprop="stylesheet"/>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="stylesheet"/><link href="www.example.com" itemprop="stylesheet"/>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="stylesheet"/><link href="www.example.com" itemprop="stylesheet"/>Yo!</body></html>',
                0
            ),
            'link - missing href' => array(
                '<html><head><title>Asdf1</title><link rel="help"/></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><link rel="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><link rel="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2
            ),
            'link - missing rel' => array(
                '<html><head><title>Asdf1</title><link href="www.example.com"/></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><link href="www.example.com"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><link href="www.example.com"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2
            ),
            'link - both rel and itemprop' => array(
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help"  itemprop="help"/></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help" itemprop="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help" itemprop="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2
            ),
            'link - incorrect usage in body' => array(
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="help"/>Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="help"/>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="help"/>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                2
            ),
            'meta - correct usage' => array(
                '<html><head><title>Asdf1</title><meta name="keywords" content="test" /></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><meta name="keywords" content="test"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><meta name="keywords" content="test"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><meta name="keywords" content="test"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><meta name="keywords" content="test"/></head><body>Yo!</body></html>',
                0
            ),
            'meta - missing content' => array(
                '<html><head><title>Asdf1</title><meta name="keywords"/></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><meta name="keywords"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><meta name="keywords" content=""/></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title><meta name="keywords" content=""/></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title><meta name="keywords" content=""/></head><body>Yo!</body></html>',
                1
            ),
            'meta - with charset child of head' => array(
                '<html><head><title>Asdf1</title><meta charset="UTF-8"/></head><body>Yo!<meta charset="utf-16"/></body></html>',
                '<html><head><title>Asdf1</title><meta charset="UTF-8"/></head><body>Yo!<meta charset="utf-16"/></body></html>',
                0,
                '<html><head><title>Asdf1</title><meta charset="utf-8"/></head><body>Yo!<meta charset="utf-16"/></body></html>',
                3,
                '<html><head><title>Asdf1</title><meta charset="utf-8"/></head><body>Yo!</body></html>',
                3,
                '<html><head><title>Asdf1</title><meta charset="utf-8"/></head><body>Yo!</body></html>',
                3
            ),
            'ol - contains incorrect tokens and elements' => array(
                '<ol class="qwerty"><!-- <h1>bad</h1> --><li>asdf1</li>asdf2<script><![CDATA[asdf]]></script><div>asdf3</div></ol>',
                '<ol class="qwerty"><!-- <h1>bad</h1> --><li>asdf1</li>asdf2<script><![CDATA[asdf]]></script><div>asdf3</div></ol>',
                0,
                '<ol class="qwerty"><!-- <h1>bad</h1> --><li>asdf1</li>asdf2<script><![CDATA[asdf]]></script><div>asdf3</div></ol>',
                0,
                '<ol class="qwerty"><!-- <h1>bad</h1> --><li>asdf1</li><script><![CDATA[asdf]]></script></ol>',
                2,
                '<ol class="qwerty"><!-- <h1>bad</h1> --><li>asdf1</li><script><![CDATA[asdf]]></script></ol>',
                2
            ),
            'style - correct usage' => array(
                '<html><head><title>Asdf1</title><style>/* Body */ body { color: green; }</style></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><style>/* Body */ body { color: green; }</style></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><style>/* Body */ body { color: green; }</style></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><style>/* Body */ body { color: green; }</style></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><style>/* Body */ body { color: green; }</style></head><body>Yo!</body></html>',
                0
            ),
            'title contains comment' => array(
                '<html><head><title>Asd<!-- just a comment -->f1</title></head><body>Yo!</body></html>',
                '<html><head><title>Asd<!-- just a comment -->f1</title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asd<!-- just a comment -->f1</title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asd<!-- just a comment -->f1</title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asd<!-- just a comment -->f1</title></head><body>Yo!</body></html>',
                0
            ),
            'title contains markup' => array(
                '<html><head><title>Asd<b>f1</b></title></head><body>Yo!</body></html>',
                '<html><head><title>Asd<b>f1</b></title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asd<b>f1</b></title></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asd</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asd</title></head><body>Yo!</body></html>',
                1
            ),
            'ul - contains incorrect tokens and elements' => array(
                '<ul><!-- <h1>bad</h1> --><li>asdf1</li>asdf2<script><![CDATA[asdf]]></script><div>asdf3</div></ul>',
                '<ul><!-- <h1>bad</h1> --><li>asdf1</li>asdf2<script><![CDATA[asdf]]></script><div>asdf3</div></ul>',
                0,
                '<ul><!-- <h1>bad</h1> --><li>asdf1</li>asdf2<script><![CDATA[asdf]]></script><div>asdf3</div></ul>',
                0,
                '<ul><!-- <h1>bad</h1> --><li>asdf1</li><script><![CDATA[asdf]]></script></ul>',
                2,
                '<ul><!-- <h1>bad</h1> --><li>asdf1</li><script><![CDATA[asdf]]></script></ul>',
                2
            )
        );
    }

    /**
     * @dataProvider cleanWithElementRemovalDataProvider
     */
    public function testCleanWithElementRemoval($removedElements,
                                                $html,
                                                $expectedOutput,
                                                $expectedLogCount)
    {
        $groundskeeper = new Groundskeeper(array(
            'element-blacklist' => $removedElements
        ));
        $testableLogger = new TestableLogger();
        $groundskeeper->setLogger($testableLogger);
        $this->assertEquals(
            $expectedOutput,
            $groundskeeper->clean($html)
        );
        $this->assertEquals(
            $expectedLogCount,
            count($testableLogger->getLogs())
        );
    }

    public function cleanWithElementRemovalDataProvider()
    {
        return array(
            'none' => array(
                '',
                '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>',
                '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>',
                0
            ),
            'single closed element' => array(
                'br',
                '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>',
                '<div class="asdf1">asdf2<i>asdf3</i><em>asdf4</em>asdf5</div>',
                1
            ),
            'single open element' => array(
                'em',
                '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>',
                '<div class="asdf1">asdf2<i>asdf3</i><br/>asdf5</div>',
                1
            ),
            'multiple elements' => array(
                'em,br',
                '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>',
                '<div class="asdf1">asdf2<i>asdf3</i>asdf5</div>',
                2
            ),
            'parent element' => array(
                'div',
                '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>',
                '',
                1
            ),
            'parent with other valid elements' => array(
                'em,div,i,meta',
                '<div class="asdf1">asdf2<i>asdf3</i><br/><em>asdf4</em>asdf5</div>',
                '',
                1
            )
        );
    }

    /**
     * @dataProvider cleanWithTypeRemovalDataProvider
     */
    public function testCleanWithTypeRemoval($removedTypes,
                                             $html,
                                             $expectedOutput,
                                             $expectedLogCount,
                                             $debugOutputLog = false)
    {
        $groundskeeper = new Groundskeeper(array(
            'type-blacklist' => $removedTypes
        ));
        $testableLogger = new TestableLogger();
        $groundskeeper->setLogger($testableLogger);
        $result = $groundskeeper->clean($html);
        if ($debugOutputLog) {
            var_dump($testableLogger->getLogs());
        }

        $this->assertEquals(
            $expectedOutput,
            $result
        );
        $this->assertEquals(
            $expectedLogCount,
            count($testableLogger->getLogs())
        );
    }

    public function cleanWithTypeRemovalDataProvider()
    {
        return array(
            'none' => array(
                '',
                '<!-- comment --><div class="asdf1">asdf5</div>',
                '<!-- comment --><div class="asdf1">asdf5</div>',
                0
            ),
            'comment only' => array(
                'comment',
                '<!-- comment --><div class="asdf1">asdf5</div>',
                '<div class="asdf1">asdf5</div>',
                1
            ),
            'comment and cdata' => array(
                'comment,cdata',
                '<!-- comment --><div class="asdf1">asdf5</div>',
                '<div class="asdf1">asdf5</div>',
                1
            ),
            'text only' => array(
                'text',
                '<!-- comment --><div class="asdf1">asdf5</div>',
                '<!-- comment --><div class="asdf1"></div>',
                1
            ),
            'php only' => array(
                'php',
                '<!-- comment --><div class="asdf1"><?php echo "asdf5"; ?></div>',
                '<!-- comment --><div class="asdf1"></div>',
                1
            )
        );
    }
}
