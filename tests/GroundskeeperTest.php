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
                0,
                '<div>asdf2</div>',
                1,
                '<div>asdf2</div>',
                1
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
            'element with attrribute whose key and value are the same' => array(
                '<asdf id="id">Text goes here</asdf>',
                '<asdf id="id">Text goes here</asdf>',
                0,
                '<asdf id="id">Text goes here</asdf>',
                0,
                '<asdf id="id">Text goes here</asdf>',
                0,
                '<asdf id="id">Text goes here</asdf>',
                0
            ),
            'element with boolean attribute' => array(
                '<form novalidate="novalidate"></form>',
                '<form novalidate="novalidate"></form>',
                0,
                '<form novalidate></form>',
                1,
                '<form novalidate></form>',
                1,
                '<form novalidate></form>',
                1
            ),
            'a - correct usage' => array(
                '<a href="https://www.example.com">Example.com</a>',
                '<a href="https://www.example.com">Example.com</a>',
                0,
                '<a href="https://www.example.com">Example.com</a>',
                0,
                '<a href="https://www.example.com">Example.com</a>',
                0,
                '<a href="https://www.example.com">Example.com</a>',
                0
            ),
            'a - correct usage - relative url' => array(
                '<a href="/asdf">Example.com</a>',
                '<a href="/asdf">Example.com</a>',
                0,
                '<a href="/asdf">Example.com</a>',
                0,
                '<a href="/asdf">Example.com</a>',
                0,
                '<a href="/asdf">Example.com</a>',
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
            'aside, ins, and del - correct usage' => array(
                '<aside><ins datetime="2016-05-16"><p>I like fruit.</p></ins><del datetime="2016-05-16"><p>I like nuts.</p></del></aside>',
                '<aside><ins datetime="2016-05-16"><p>I like fruit.</p></ins><del datetime="2016-05-16"><p>I like nuts.</p></del></aside>',
                0,
                '<aside><ins datetime="2016-05-16"><p>I like fruit.</p></ins><del datetime="2016-05-16"><p>I like nuts.</p></del></aside>',
                0,
                '<aside><ins datetime="2016-05-16"><p>I like fruit.</p></ins><del datetime="2016-05-16"><p>I like nuts.</p></del></aside>',
                0,
                '<aside><ins datetime="2016-05-16"><p>I like fruit.</p></ins><del datetime="2016-05-16"><p>I like nuts.</p></del></aside>',
                0
            ),
            'audio - correct usage' => array(
                '<audio src="brave.MP4">
    Intro<track src=introduction.mp4 srclang=en label="Intro">
</audio>',
                '<audio src="brave.MP4"> Intro<track src="introduction.mp4" srclang="en" label="Intro"/></audio>',
                0,
                '<audio src="brave.MP4"> Intro<track src="introduction.mp4" srclang="en" label="Intro"/></audio>',
                0,
                '<audio src="brave.MP4"> Intro<track src="introduction.mp4" srclang="en" label="Intro"/></audio>',
                0,
                '<audio src="brave.MP4"> Intro<track src="introduction.mp4" srclang="en" label="Intro"/></audio>',
                0
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
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'base - must be child of head' => array(
                '<html><head><title>Asdf1</title></head><body>Yo!<base href="www.example.com"/></body></html>',
                '<html><head><title>Asdf1</title></head><body>Yo!<base href="www.example.com"/></body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!<base href="www.example.com"/></body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
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
                1,
                '<html><head><title>Asdf1</title></head><body></body></html>',
                2,
                '<html><head><title>Asdf1</title></head><body></body></html>',
                2
            ),
            'body - no html element parent' => array(
                '<div><body>asdf1</body>asdf2</div>',
                '<div><body>asdf1</body>asdf2</div>',
                0,
                '<div><body>asdf1</body>asdf2</div>',
                0,
                '<div>asdf2</div>',
                1,
                '<div>asdf2</div>',
                1
            ),
            'button - correct usage' => array(
                '<button type=button onclick="alert(\'This 15-20 minute piece was composed by George Gershwin.\')">Show hint</button>',
                '<button type="button" onclick="alert(\'This 15-20 minute piece was composed by George Gershwin.\')">Show hint</button>',
                0,
                '<button type="button" onclick="alert(\'This 15-20 minute piece was composed by George Gershwin.\')">Show hint</button>',
                0,
                '<button type="button" onclick="alert(\'This 15-20 minute piece was composed by George Gershwin.\')">Show hint</button>',
                0,
                '<button type="button" onclick="alert(\'This 15-20 minute piece was composed by George Gershwin.\')">Show hint</button>',
                0
            ),
            'button - incorrect children' => array(
                '<button type="button"><a href="http://www.example.com">asdf</a></button>',
                '<button type="button"><a href="http://www.example.com">asdf</a></button>',
                0,
                '<button type="button"><a href="http://www.example.com">asdf</a></button>',
                0,
                '<button type="button"></button>',
                1,
                '<button type="button"></button>',
                1
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
                2,
                '<div>Whoa!</div>',
                2
            ),
            'dt - contains wrong element' => array(
                '<dl><dt>Authors<section>Hmmm...</section></dt><dd>Kevin</dd></dl>',
                '<dl><dt>Authors<section>Hmmm...</section></dt><dd>Kevin</dd></dl>',
                0,
                '<dl><dt>Authors<section>Hmmm...</section></dt><dd>Kevin</dd></dl>',
                0,
                '<dl><dt>Authors</dt><dd>Kevin</dd></dl>',
                1,
                '<dl><dt>Authors</dt><dd>Kevin</dd></dl>',
                1
            ),
            'footer - bad child' => array(
                '<div><footer>asdf1<footer>asdf2</footer>asdf3</footer></div>',
                '<div><footer>asdf1<footer>asdf2</footer>asdf3</footer></div>',
                0,
                '<div><footer>asdf1<footer>asdf2</footer>asdf3</footer></div>',
                0,
                '<div><footer>asdf1asdf3</footer></div>',
                1,
                '<div><footer>asdf1asdf3</footer></div>',
                1
            ),
            'form - correct usage' => array(
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"></label><input type="submit" value="Search..."></form>',
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/></label><input type="submit" value="Search..."/></form>',
                0
            ),
            'form - descendent form' => array(
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"><form action="yo!" method="post"><input type="hidden" value="9"/></form></label><input type="submit" value="Search..."></form>',
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/><form action="yo!" method="post"><input type="hidden" value="9"/></form></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/><form action="yo!" method="post"><input type="hidden" value="9"/></form></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/></label><input type="submit" value="Search..."/></form>',
                1,
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/></label><input type="submit" value="Search..."/></form>',
                1
            ),
            'form - descendent form - testing error' => array(
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"><div action="yo!" method="post"><input type="hidden" value="9"/></div></label><input type="submit" value="Search..."></form>',
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/><div action="yo!" method="post"><input type="hidden" value="9"/></div></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/><div action="yo!" method="post"><input type="hidden" value="9"/></div></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/><div><input type="hidden" value="9"/></div></label><input type="submit" value="Search..."/></form>',
                2,
                '<form action="http://www.google.com/search" method="get"><label>Google: <input type="search" name="q"/><div><input type="hidden" value="9"/></div></label><input type="submit" value="Search..."/></form>',
                2
            ),
            'head - child of non-html element' => array(
                '<html><div><head><title>Asdf1</title></head></div><body>Yo!</body></html>',
                '<html><div><head><title>Asdf1</title></head></div><body>Yo!</body></html>',
                0,
                '<html><head><title></title></head><div><head><title>Asdf1</title></head></div><body>Yo!</body></html>',
                2,
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
            'head - no html element parent' => array(
                '<div><head>asdf1</head>asdf2</div>',
                '<div><head>asdf1</head>asdf2</div>',
                0,
                '<div><head><title></title>asdf1</head>asdf2</div>',
                1,
                '<div>asdf2</div>',
                2,
                '<div>asdf2</div>',
                2
            ),
            'header - bad child' => array(
                '<div><footer>asdf1<header>asdf2</header>asdf3</footer></div>',
                '<div><footer>asdf1<header>asdf2</header>asdf3</footer></div>',
                0,
                '<div><footer>asdf1<header>asdf2</header>asdf3</footer></div>',
                0,
                '<div><footer>asdf1asdf3</footer></div>',
                1,
                '<div><footer>asdf1asdf3</footer></div>',
                1
            ),
            'header - header ancestor' => array(
                '<header><div><header>asdf1</header>asdf2</div><hr/>asdf3</header>',
                '<header><div><header>asdf1</header>asdf2</div><hr/>asdf3</header>',
                0,
                '<header><div><header>asdf1</header>asdf2</div><hr/>asdf3</header>',
                0,
                '<header><div>asdf2</div><hr/>asdf3</header>',
                1,
                '<header><div>asdf2</div><hr/>asdf3</header>',
                1
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
                0,
                '<div>asdf</div>',
                1,
                '<div>asdf</div>',
                1
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
            'iframe - correct usage' => array(
                '<html><head><title>Asdf1</title></head><body><iframe src="https://www.example.com"><html>yo!</html></iframe></body></html>',
                '<html><head><title>Asdf1</title></head><body><iframe src="https://www.example.com"></iframe></body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><iframe src="https://www.example.com"></iframe></body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><iframe src="https://www.example.com"></iframe></body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><iframe src="https://www.example.com"></iframe></body></html>',
                0
            ),
            'img - malformed height and width' => array(
                '<img height="2.4" width="5e2" />',
                '<img height="2.4" width="5e2"/>',
                0,
                '<img height="2" width="5"/>',
                2,
                '<img height="2" width="5"/>',
                2,
                '<img height="2" width="5"/>',
                2
            ),
            'img - malformed height and width again' => array(
                '<img height="-2.4" width />',
                '<img height="-2.4" width/>',
                0,
                '<img height="-2" width/>',
                1,
                '<img/>',
                3,
                '<img/>',
                3
            ),
            'label - malformed children' => array(
                '<form action="form.html" method="POST"><label for="asdf"><input id="asdf" type="hidden" value="9"/><label>Whoa!</label></label></form>',
                '<form action="form.html" method="POST"><label for="asdf"><input id="asdf" type="hidden" value="9"/><label>Whoa!</label></label></form>',
                0,
                '<form action="form.html" method="post"><label for="asdf"><input id="asdf" type="hidden" value="9"/><label>Whoa!</label></label></form>',
                1,
                '<form action="form.html" method="post"><label for="asdf"><input id="asdf" type="hidden" value="9"/></label></form>',
                2,
                '<form action="form.html" method="post"><label for="asdf"><input id="asdf" type="hidden" value="9"/></label></form>',
                2
            ),
            'li - menu' => array(
                '<menu><li>asdf</li></menu><menu type="toolbar"><li>asdf</li></menu>',
                '<menu><li>asdf</li></menu><menu type="toolbar"><li>asdf</li></menu>',
                0,
                '<menu type="context"><li>asdf</li></menu><menu type="toolbar"><li>asdf</li></menu>',
                1,
                '<menu type="context"></menu><menu type="toolbar"><li>asdf</li></menu>',
                2,
                '<menu type="context"></menu><menu type="toolbar"><li>asdf</li></menu>',
                2
            ),
            'li - wrong parent' => array(
                '<div><li>asdf</li></div>',
                '<div><li>asdf</li></div>',
                0,
                '<div><li>asdf</li></div>',
                0,
                '<div></div>',
                1,
                '<div></div>',
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
                '<html><head><title>Asdf1</title></head><body><link href="www.example1.com" rel="stylesheet"/><link href="www.example2.com" itemprop="stylesheet"/>Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body><link href="www.example1.com" rel="stylesheet"/><link href="www.example2.com" itemprop="stylesheet"/>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><link href="www.example1.com" rel="stylesheet"/><link href="www.example2.com" itemprop="stylesheet"/>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><link href="www.example1.com" rel="stylesheet"/><link href="www.example2.com" itemprop="stylesheet"/>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><link href="www.example1.com" rel="stylesheet"/><link href="www.example2.com" itemprop="stylesheet"/>Yo!</body></html>',
                0
            ),
            'link - missing href' => array(
                '<html><head><title>Asdf1</title><link rel="help"/></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><link rel="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><link rel="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'link - missing rel' => array(
                '<html><head><title>Asdf1</title><link href="www.example.com"/></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><link href="www.example.com"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><link href="www.example.com"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'link - both rel and itemprop' => array(
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help"  itemprop="help"/></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help" itemprop="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><link href="www.example.com" rel="help" itemprop="help"/></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'link - incorrect usage in body' => array(
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="help"/>Yo!</body></html>',
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="help"/>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body><link href="www.example.com" rel="help"/>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1,
                '<html><head><title>Asdf1</title></head><body>Yo!</body></html>',
                1
            ),
            'map and area - correct usage' => array(
                '<p>Please select a shape:<img src="shapes.png" usemap="#shapes" alt="A red hollow box"><map name="shapes"><area shape=rect coords="50,50,100,100"><!-- the hole in the red box --><area shape=rect coords="25,25,125,125" href="red.html" alt="Red box."></map></p>',
                '<p>Please select a shape:<img src="shapes.png" usemap="#shapes" alt="A red hollow box"/><map name="shapes"><area shape="rect" coords="50,50,100,100"/><!-- the hole in the red box --><area shape="rect" coords="25,25,125,125" href="red.html" alt="Red box."/></map></p>',
                0,
                '<p>Please select a shape:<img src="shapes.png" usemap="#shapes" alt="A red hollow box"/><map name="shapes"><area shape="rect" coords="50,50,100,100"/><!-- the hole in the red box --><area shape="rect" coords="25,25,125,125" href="red.html" alt="Red box."/></map></p>',
                0,
                '<p>Please select a shape:<img src="shapes.png" usemap="#shapes" alt="A red hollow box"/><map name="shapes"><area shape="rect" coords="50,50,100,100"/><!-- the hole in the red box --><area shape="rect" coords="25,25,125,125" href="red.html" alt="Red box."/></map></p>',
                0,
                '<p>Please select a shape:<img src="shapes.png" usemap="#shapes" alt="A red hollow box"/><map name="shapes"><area shape="rect" coords="50,50,100,100"/><!-- the hole in the red box --><area shape="rect" coords="25,25,125,125" href="red.html" alt="Red box."/></map></p>',
                0
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
                1,
                '<html><head><title>Asdf1</title><meta charset="utf-8"/></head><body>Yo!</body></html>',
                2,
                '<html><head><title>Asdf1</title><meta charset="utf-8"/></head><body>Yo!</body></html>',
                2
            ),
            'ol - li[value] children' => array(
                '<ol><li value=1>asdf1</li><li value=3></li></ol>',
                '<ol><li value="1">asdf1</li><li value="3"></li></ol>',
                0,
                '<ol><li value="1">asdf1</li><li value="3"></li></ol>',
                0,
                '<ol><li value="1">asdf1</li><li value="3"></li></ol>',
                0,
                '<ol><li value="1">asdf1</li><li value="3"></li></ol>',
                0
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
            'option and select - correct usage' => array(
                '<p><label for="unittype">Select unit type:</label><select id="unittype" name="unittype"><option value="1"> Miner </option><option value="2"> Puffer </option><option value="3" selected> Snipey </option><option value="4"> Max </option></select></p>',
                '<p><label for="unittype">Select unit type:</label><select id="unittype" name="unittype"><option value="1"> Miner </option><option value="2"> Puffer </option><option value="3" selected> Snipey </option><option value="4"> Max </option></select></p>',
                0,
                '<p><label for="unittype">Select unit type:</label><select id="unittype" name="unittype"><option value="1"> Miner </option><option value="2"> Puffer </option><option value="3" selected> Snipey </option><option value="4"> Max </option></select></p>',
                0,
                '<p><label for="unittype">Select unit type:</label><select id="unittype" name="unittype"><option value="1"> Miner </option><option value="2"> Puffer </option><option value="3" selected> Snipey </option><option value="4"> Max </option></select></p>',
                0,
                '<p><label for="unittype">Select unit type:</label><select id="unittype" name="unittype"><option value="1"> Miner </option><option value="2"> Puffer </option><option value="3" selected> Snipey </option><option value="4"> Max </option></select></p>',
                0
            ),
            'option - malformed parent' => array(
                '<p><option value="1">Asdf</option></p>',
                '<p><option value="1">Asdf</option></p>',
                0,
                '<p><option value="1">Asdf</option></p>',
                0,
                '<p></p>',
                1,
                '<p></p>',
                1
            ),
            'q' => array(
                '<div>asdf1<q cite="asdf2" well="asdf3">asdf4</q>asdf5</div>',
                '<div>asdf1<q cite="asdf2" well="asdf3">asdf4</q>asdf5</div>',
                0,
                '<div>asdf1<q cite="asdf2" well="asdf3">asdf4</q>asdf5</div>',
                0,
                '<div>asdf1<q cite="asdf2">asdf4</q>asdf5</div>',
                1,
                '<div>asdf1<q cite="asdf2">asdf4</q>asdf5</div>',
                1
            ),
            'rp and rt - incorrect parent' => array(
                '<div><rp> (</rp><rt>かん</rt><rp>) </rp></div>',
                '<div><rp> (</rp><rt>かん</rt><rp>) </rp></div>',
                0,
                '<div><rp> (</rp><rt>かん</rt><rp>) </rp></div>',
                0,
                '<div></div>',
                3,
                '<div></div>',
                3
            ),
            'ruby - correct usage' => array(
                '<ruby>漢<rp> (</rp><rt>かん</rt><rp>) </rp>字<rp> (</rp><rt>じ</rt><rp>) </rp></ruby>',
                '<ruby>漢<rp> (</rp><rt>かん</rt><rp>) </rp>字<rp> (</rp><rt>じ</rt><rp>) </rp></ruby>',
                0,
                '<ruby>漢<rp> (</rp><rt>かん</rt><rp>) </rp>字<rp> (</rp><rt>じ</rt><rp>) </rp></ruby>',
                0,
                '<ruby>漢<rp> (</rp><rt>かん</rt><rp>) </rp>字<rp> (</rp><rt>じ</rt><rp>) </rp></ruby>',
                0,
                '<ruby>漢<rp> (</rp><rt>かん</rt><rp>) </rp>字<rp> (</rp><rt>じ</rt><rp>) </rp></ruby>',
                0
            ),
            'style - correct usage' => array(
                '<html><head><title>Asdf1</title><style media="all">/* Body */ body { color: green; }</style></head><body>Yo!</body></html>',
                '<html><head><title>Asdf1</title><style media="all">/* Body */ body { color: green; }</style></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><style media="all">/* Body */ body { color: green; }</style></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><style media="all">/* Body */ body { color: green; }</style></head><body>Yo!</body></html>',
                0,
                '<html><head><title>Asdf1</title><style media="all">/* Body */ body { color: green; }</style></head><body>Yo!</body></html>',
                0
            ),
            'table - correct usage' => array(
                '<table><thead><tr><th>Animal</th><th colspan=2>Color</th></tr></thead><!-- body --><tbody><tr><td>Ant</td><td colspan=2>Black</td></tr></tbody><tfoot><tr><td colspan=2>---</td><td>---</td></tr></tfoot></table>',
                '<table><thead><tr><th>Animal</th><th colspan="2">Color</th></tr></thead><!-- body --><tbody><tr><td>Ant</td><td colspan="2">Black</td></tr></tbody><tfoot><tr><td colspan="2">---</td><td>---</td></tr></tfoot></table>',
                0,
                '<table><thead><tr><th>Animal</th><th colspan="2">Color</th></tr></thead><!-- body --><tbody><tr><td>Ant</td><td colspan="2">Black</td></tr></tbody><tfoot><tr><td colspan="2">---</td><td>---</td></tr></tfoot></table>',
                0,
                '<table><thead><tr><th>Animal</th><th colspan="2">Color</th></tr></thead><!-- body --><tbody><tr><td>Ant</td><td colspan="2">Black</td></tr></tbody><tfoot><tr><td colspan="2">---</td><td>---</td></tr></tfoot></table>',
                0,
                '<table><thead><tr><th>Animal</th><th colspan="2">Color</th></tr></thead><!-- body --><tbody><tr><td>Ant</td><td colspan="2">Black</td></tr></tbody><tfoot><tr><td colspan="2">---</td><td>---</td></tr></tfoot></table>',
                0
            ),
            'table - inappropriate children' => array(
                '<table>Whoa!<hr /><tr><!-- comment --></tr></table>',
                '<table>Whoa!<hr/><tr><!-- comment --></tr></table>',
                0,
                '<table>Whoa!<hr/><tr><!-- comment --></tr></table>',
                0,
                '<table><tr><!-- comment --></tr></table>',
                2,
                '<table><tr><!-- comment --></tr></table>',
                2
            ),
            'tbody,thead, and tfoot - wrong parent' => array(
                '<div><thead>whoa</thead><tbody>whoa</tbody><tfoot>whoa</tfoot></div>',
                '<div><thead>whoa</thead><tbody>whoa</tbody><tfoot>whoa</tfoot></div>',
                0,
                '<div><thead>whoa</thead><tbody>whoa</tbody><tfoot>whoa</tfoot></div>',
                0,
                '<div></div>',
                3,
                '<div></div>',
                3
            ),
            'tbody,thead, and tfoot - text children' => array(
                '<table><thead>whoa</thead><tbody>whoa</tbody><tfoot>whoa</tfoot></table>',
                '<table><thead>whoa</thead><tbody>whoa</tbody><tfoot>whoa</tfoot></table>',
                0,
                '<table><thead>whoa</thead><tbody>whoa</tbody><tfoot>whoa</tfoot></table>',
                0,
                '<table><thead></thead><tbody></tbody><tfoot></tfoot></table>',
                3,
                '<table><thead></thead><tbody></tbody><tfoot></tfoot></table>',
                3
            ),
            'tbody,thead, and tfoot - wrong element children' => array(
                '<table><thead><!-- comment --><div>whoa</div></thead><tbody><!-- comment --><div>whoa</div></tbody><tfoot><!-- comment --><div>whoa</div></tfoot></table>',
                '<table><thead><!-- comment --><div>whoa</div></thead><tbody><!-- comment --><div>whoa</div></tbody><tfoot><!-- comment --><div>whoa</div></tfoot></table>',
                0,
                '<table><thead><!-- comment --><div>whoa</div></thead><tbody><!-- comment --><div>whoa</div></tbody><tfoot><!-- comment --><div>whoa</div></tfoot></table>',
                0,
                '<table><thead><!-- comment --></thead><tbody><!-- comment --></tbody><tfoot><!-- comment --></tfoot></table>',
                3,
                '<table><thead><!-- comment --></thead><tbody><!-- comment --></tbody><tfoot><!-- comment --></tfoot></table>',
                3
            ),
            'td and th - incorrect parents' => array(
                '<div><th>oops</th></div><div><td>oops</td></div>',
                '<div><th>oops</th></div><div><td>oops</td></div>',
                0,
                '<div><th>oops</th></div><div><td>oops</td></div>',
                0,
                '<div></div><div></div>',
                2,
                '<div></div><div></div>',
                2
            ),
            'title - contains comment' => array(
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
            'textarea - correct usage' => array(
                '<form action="http://www.google.com/search" method="get"><label>Google: <textarea cols=5>asdf</textarea></label><input type="submit" value="Search..."></form>',
                '<form action="http://www.google.com/search" method="get"><label>Google: <textarea cols="5">asdf</textarea></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <textarea cols="5">asdf</textarea></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <textarea cols="5">asdf</textarea></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <textarea cols="5">asdf</textarea></label><input type="submit" value="Search..."/></form>',
                0
            ),
            'textarea - invalid content' => array(
                '<form action="http://www.google.com/search" method="get"><label>Google: <textarea cols=5>asdf1<div>asdf2</div></textarea></label><input type="submit" value="Search..."></form>',
                '<form action="http://www.google.com/search" method="get"><label>Google: <textarea cols="5">asdf1<div>asdf2</div></textarea></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <textarea cols="5">asdf1<div>asdf2</div></textarea></label><input type="submit" value="Search..."/></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><label>Google: <textarea cols="5">asdf1</textarea></label><input type="submit" value="Search..."/></form>',
                1,
                '<form action="http://www.google.com/search" method="get"><label>Google: <textarea cols="5">asdf1</textarea></label><input type="submit" value="Search..."/></form>',
                1
            ),
            'tr - wrong parent and children' => array(
                '<div><tr>Hmm...</tr></div><table><tr>asdf1<div>asdf2</div><td>yes</td></tr></table>',
                '<div><tr>Hmm...</tr></div><table><tr>asdf1<div>asdf2</div><td>yes</td></tr></table>',
                0,
                '<div><tr>Hmm...</tr></div><table><tr>asdf1<div>asdf2</div><td>yes</td></tr></table>',
                0,
                '<div></div><table><tr><td>yes</td></tr></table>',
                3,
                '<div></div><table><tr><td>yes</td></tr></table>',
                3
            ),
            'ul - contains incorrect tokens and elements' => array(
                '<li id="asdf">asdf</li><ul><!-- <h1>bad</h1> --><li value="2">asdf1</li>asdf2<script><![CDATA[asdf]]></script><div>asdf3</div></ul>',
                '<li id="asdf">asdf</li><ul><!-- <h1>bad</h1> --><li value="2">asdf1</li>asdf2<script><![CDATA[asdf]]></script><div>asdf3</div></ul>',
                0,
                '<li id="asdf">asdf</li><ul><!-- <h1>bad</h1> --><li value="2">asdf1</li>asdf2<script><![CDATA[asdf]]></script><div>asdf3</div></ul>',
                0,
                '<li id="asdf">asdf</li><ul><!-- <h1>bad</h1> --><li>asdf1</li><script><![CDATA[asdf]]></script></ul>',
                3,
                '<li id="asdf">asdf</li><ul><!-- <h1>bad</h1> --><li>asdf1</li><script><![CDATA[asdf]]></script></ul>',
                3
            ),
            'ul - li[value] children' => array(
                '<ul><li value=1>asdf1</li><li value=3>asdf3</li></ul>',
                '<ul><li value="1">asdf1</li><li value="3">asdf3</li></ul>',
                0,
                '<ul><li value="1">asdf1</li><li value="3">asdf3</li></ul>',
                0,
                '<ul><li>asdf1</li><li>asdf3</li></ul>',
                2,
                '<ul><li>asdf1</li><li>asdf3</li></ul>',
                2
            ),
            'video - correct usage' => array(
                '<video src="brave.webm">
    <track kind=subtitles src=brave.en.vtt srclang=en label="English">
    <track kind=captions src=brave.en.hoh.vtt srclang=en label="English for the Hard of Hearing">
</video>',
                '<video src="brave.webm"><track kind="subtitles" src="brave.en.vtt" srclang="en" label="English"/><track kind="captions" src="brave.en.hoh.vtt" srclang="en" label="English for the Hard of Hearing"/></video>',
                0,
                '<video src="brave.webm"><track kind="subtitles" src="brave.en.vtt" srclang="en" label="English"/><track kind="captions" src="brave.en.hoh.vtt" srclang="en" label="English for the Hard of Hearing"/></video>',
                0,
                '<video src="brave.webm"><track kind="subtitles" src="brave.en.vtt" srclang="en" label="English"/><track kind="captions" src="brave.en.hoh.vtt" srclang="en" label="English for the Hard of Hearing"/></video>',
                0,
                '<video src="brave.webm"><track kind="subtitles" src="brave.en.vtt" srclang="en" label="English"/><track kind="captions" src="brave.en.hoh.vtt" srclang="en" label="English for the Hard of Hearing"/></video>',
                0
            ),
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
