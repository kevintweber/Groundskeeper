<?php

namespace Groundskeeper\Tests;

use Groundskeeper\Groundskeeper;
use PHPUnit\Framework\TestCase;
use Psr\Log\NullLogger;

class GroundskeeperTest extends TestCase
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
    <a href="http://www.example.com">Intro</a><track src=introduction.mp4 srclang=en label="Intro">
</audio>',
                '<audio src="brave.MP4"><a href="http://www.example.com">Intro</a><track src="introduction.mp4" srclang="en" label="Intro"/></audio>',
                0,
                '<audio src="brave.MP4"><a href="http://www.example.com">Intro</a><track src="introduction.mp4" srclang="en" label="Intro"/></audio>',
                0,
                '<audio src="brave.MP4"><a href="http://www.example.com">Intro</a><track src="introduction.mp4" srclang="en" label="Intro"/></audio>',
                0,
                '<audio src="brave.MP4"><a href="http://www.example.com">Intro</a><track src="introduction.mp4" srclang="en" label="Intro"/></audio>',
                0
            ),
            'audio - correct usage with source' => array(
                '<audio><source type="yo">brave.mp4</audio>',
                '<audio><source type="yo"/>brave.mp4</audio>',
                0,
                '<audio><source type="yo"/>brave.mp4</audio>',
                0,
                '<audio><source type="yo"/>brave.mp4</audio>',
                0,
                '<audio><source type="yo"/>brave.mp4</audio>',
                0
            ),
            'audio - incorrect children' => array(
                '<audio><div>brave.mp4</div></audio>',
                '<audio><div>brave.mp4</div></audio>',
                0,
                '<audio><div>brave.mp4</div></audio>',
                0,
                '<audio></audio>',
                1,
                '<audio></audio>',
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
            'canvas - correct usage' => array(
                '<canvas id="myCanvas" width="200" height="100"></canvas>',
                '<canvas id="myCanvas" width="200" height="100"></canvas>',
                0,
                '<canvas id="myCanvas" width="200" height="100"></canvas>',
                0,
                '<canvas id="myCanvas" width="200" height="100"></canvas>',
                0,
                '<canvas id="myCanvas" width="200" height="100"></canvas>',
                0
            ),
            'caption and source - incorrect parent' => array(
                '<div><caption>Whoa!</caption><source /></div>',
                '<div><caption>Whoa!</caption><source/></div>',
                0,
                '<div><caption>Whoa!</caption><source/></div>',
                0,
                '<div></div>',
                2,
                '<div></div>',
                2
            ),
            'col - incorrect parent' => array(
                '<div><col span=2/></div>',
                '<div><col span="2"/></div>',
                0,
                '<div><col span="2"/></div>',
                0,
                '<div></div>',
                1,
                '<div></div>',
                1
            ),
            'details - correct usage' => array(
                '<details open>
  <summary>Copyright 1999-2014.</summary>
  <p> - by Refsnes Data. All Rights Reserved.</p>
  <p>All content and graphics on this web site are the property of the company Refsnes Data.</p>
</details>',
                '<details open><summary>Copyright 1999-2014.</summary><p> - by Refsnes Data. All Rights Reserved.</p><p>All content and graphics on this web site are the property of the company Refsnes Data.</p></details>',
                0,
                '<details open><summary>Copyright 1999-2014.</summary><p> - by Refsnes Data. All Rights Reserved.</p><p>All content and graphics on this web site are the property of the company Refsnes Data.</p></details>',
                0,
                '<details open><summary>Copyright 1999-2014.</summary><p> - by Refsnes Data. All Rights Reserved.</p><p>All content and graphics on this web site are the property of the company Refsnes Data.</p></details>',
                0,
                '<details open><summary>Copyright 1999-2014.</summary><p> - by Refsnes Data. All Rights Reserved.</p><p>All content and graphics on this web site are the property of the company Refsnes Data.</p></details>',
                0
            ),
            'dfn - correct usage' => array(
                '<p>The <dfn><abbr title="Garage Door Opener">GDO</abbr></dfn>
is a device that allows off-world teams to open the iris.</p>',
                '<p>The <dfn><abbr title="Garage Door Opener">GDO</abbr></dfn> is a device that allows off-world teams to open the iris.</p>',
                0,
                '<p>The <dfn><abbr title="Garage Door Opener">GDO</abbr></dfn> is a device that allows off-world teams to open the iris.</p>',
                0,
                '<p>The <dfn><abbr title="Garage Door Opener">GDO</abbr></dfn> is a device that allows off-world teams to open the iris.</p>',
                0,
                '<p>The <dfn><abbr title="Garage Door Opener">GDO</abbr></dfn> is a device that allows off-world teams to open the iris.</p>',
                0
            ),
            'dfn - incorrect children' => array(
                '<dfn>The <dfn><abbr title="Garage Door Opener">GDO</abbr></dfn><div><dfn>Whoa</dfn></div>is a garage door opener.</dfn>',
                '<dfn>The <dfn><abbr title="Garage Door Opener">GDO</abbr></dfn><div><dfn>Whoa</dfn></div>is a garage door opener.</dfn>',
                0,
                '<dfn>The <dfn><abbr title="Garage Door Opener">GDO</abbr></dfn><div><dfn>Whoa</dfn></div>is a garage door opener.</dfn>',
                0,
                '<dfn>The <div></div>is a garage door opener.</dfn>',
                2,
                '<dfn>The <div></div>is a garage door opener.</dfn>',
                2
            ),
            'dl - correct usage' => array(
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
            'embed - correct usage' => array(
                '<div><embed src="catgame.swf"></div>',
                '<div><embed src="catgame.swf"/></div>',
                0,
                '<div><embed src="catgame.swf"/></div>',
                0,
                '<div><embed src="catgame.swf"/></div>',
                0,
                '<div><embed src="catgame.swf"/></div>',
                0
            ),
            'figure - correct usage' => array(
                '<figure>
  <img src="/macaque.jpg" alt="Macaque in the trees">
  <figcaption>A cheeky macaque, Lower Kintaganban River, Borneo. Original by <a href="http://www.flickr.com/photos/rclark/">Richard Clark</a></figcaption>
</figure>',
                '<figure><img src="/macaque.jpg" alt="Macaque in the trees"/><figcaption>A cheeky macaque, Lower Kintaganban River, Borneo. Original by <a href="http://www.flickr.com/photos/rclark/">Richard Clark</a></figcaption></figure>',
                0,
                '<figure><img src="/macaque.jpg" alt="Macaque in the trees"/><figcaption>A cheeky macaque, Lower Kintaganban River, Borneo. Original by <a href="http://www.flickr.com/photos/rclark/">Richard Clark</a></figcaption></figure>',
                0,
                '<figure><img src="/macaque.jpg" alt="Macaque in the trees"/><figcaption>A cheeky macaque, Lower Kintaganban River, Borneo. Original by <a href="http://www.flickr.com/photos/rclark/">Richard Clark</a></figcaption></figure>',
                0,
                '<figure><img src="/macaque.jpg" alt="Macaque in the trees"/><figcaption>A cheeky macaque, Lower Kintaganban River, Borneo. Original by <a href="http://www.flickr.com/photos/rclark/">Richard Clark</a></figcaption></figure>',
                0
            ),
            'figcaption - incorrect parent' => array(
                '<div>
  <img src="/macaque.jpg" alt="Macaque in the trees">
  <figcaption>A cheeky macaque, Lower Kintaganban River, Borneo. Original by <a href="http://www.flickr.com/photos/rclark/">Richard Clark</a></figcaption>
</div>',
                '<div><img src="/macaque.jpg" alt="Macaque in the trees"/><figcaption>A cheeky macaque, Lower Kintaganban River, Borneo. Original by <a href="http://www.flickr.com/photos/rclark/">Richard Clark</a></figcaption></div>',
                0,
                '<div><img src="/macaque.jpg" alt="Macaque in the trees"/><figcaption>A cheeky macaque, Lower Kintaganban River, Borneo. Original by <a href="http://www.flickr.com/photos/rclark/">Richard Clark</a></figcaption></div>',
                0,
                '<div><img src="/macaque.jpg" alt="Macaque in the trees"/></div>',
                1,
                '<div><img src="/macaque.jpg" alt="Macaque in the trees"/></div>',
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
                '<form action="http://www.google.com/search" method="get"><fieldset name="search"><label>Google: <input type="search" name="q"></label><input type="submit" value="Search..."></fieldset><output name=o for="a b"></output></form>',
                '<form action="http://www.google.com/search" method="get"><fieldset name="search"><label>Google: <input type="search" name="q"/></label><input type="submit" value="Search..."/></fieldset><output name="o" for="a b"></output></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><fieldset name="search"><label>Google: <input type="search" name="q"/></label><input type="submit" value="Search..."/></fieldset><output name="o" for="a b"></output></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><fieldset name="search"><label>Google: <input type="search" name="q"/></label><input type="submit" value="Search..."/></fieldset><output name="o" for="a b"></output></form>',
                0,
                '<form action="http://www.google.com/search" method="get"><fieldset name="search"><label>Google: <input type="search" name="q"/></label><input type="submit" value="Search..."/></fieldset><output name="o" for="a b"></output></form>',
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
            'hgroup - correct usage' => array(
                '<hgroup><!-- comment --><h1>Title</h1></hgroup>',
                '<hgroup><!-- comment --><h1>Title</h1></hgroup>',
                0,
                '<hgroup><!-- comment --><h1>Title</h1></hgroup>',
                0,
                '<hgroup><!-- comment --><h1>Title</h1></hgroup>',
                0,
                '<hgroup><!-- comment --><h1>Title</h1></hgroup>',
                0
            ),
            'hgroup - incorrect children' => array(
                '<hgroup><div>Title</div><h1>Title2</h1></hgroup>',
                '<hgroup><div>Title</div><h1>Title2</h1></hgroup>',
                0,
                '<hgroup><div>Title</div><h1>Title2</h1></hgroup>',
                0,
                '<hgroup><h1>Title2</h1></hgroup>',
                1,
                '<hgroup><h1>Title2</h1></hgroup>',
                1
            ),
            'hgroup - no valid children' => array(
                '<hgroup><div>Title</div></hgroup>',
                '<hgroup><div>Title</div></hgroup>',
                0,
                '<hgroup><div>Title</div></hgroup>',
                0,
                '',
                1,
                '',
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
                '<img height="2.4" width="5p2" />',
                '<img height="2.4" width="5p2"/>',
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
            'menu - correct usage' => array(
                '<menu type="context" id="mymenu">
  <menuitem label="Refresh" onclick="window.location.reload();" icon="ico_reload.png">
  </menuitem>
  <menu label="Share on...">
    <menuitem label="Twitter" icon="ico_twitter.png">Twitter</menuitem>
    <menuitem label="Facebook" icon="ico_facebook.png">Facebook</menuitem>
  </menu>
</menu>',
                '<menu type="context" id="mymenu"><menuitem label="Refresh" onclick="window.location.reload();" icon="ico_reload.png"></menuitem><menu label="Share on..."><menuitem label="Twitter" icon="ico_twitter.png">Twitter</menuitem><menuitem label="Facebook" icon="ico_facebook.png">Facebook</menuitem></menu></menu>',
                0,
                '<menu type="context" id="mymenu"><menuitem label="Refresh" onclick="window.location.reload();" icon="ico_reload.png"></menuitem><menu label="Share on..." type="context"><menuitem label="Twitter" icon="ico_twitter.png">Twitter</menuitem><menuitem label="Facebook" icon="ico_facebook.png">Facebook</menuitem></menu></menu>',
                1,
                '<menu type="context" id="mymenu"><menuitem label="Refresh" onclick="window.location.reload();" icon="ico_reload.png"></menuitem><menu label="Share on..." type="context"><menuitem label="Twitter" icon="ico_twitter.png">Twitter</menuitem><menuitem label="Facebook" icon="ico_facebook.png">Facebook</menuitem></menu></menu>',
                1,
                '<menu type="context" id="mymenu"><menuitem label="Refresh" onclick="window.location.reload();" icon="ico_reload.png"></menuitem><menu label="Share on..." type="context"><menuitem label="Twitter" icon="ico_twitter.png">Twitter</menuitem><menuitem label="Facebook" icon="ico_facebook.png">Facebook</menuitem></menu></menu>',
                1
            ),
            'menu - invalid children' => array(
                '<menu type="context"><!-- comment --><a href="http://www.example.com">Whoa!</a></menu>',
                '<menu type="context"><!-- comment --><a href="http://www.example.com">Whoa!</a></menu>',
                0,
                '<menu type="context"><!-- comment --><a href="http://www.example.com">Whoa!</a></menu>',
                0,
                '<menu type="context"><!-- comment --></menu>',
                1,
                '<menu type="context"><!-- comment --></menu>',
                1,
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
            'meter - correct usage' => array(
                '<meter value="0.6">60%</meter>',
                '<meter value="0.6">60%</meter>',
                0,
                '<meter value="0.6">60%</meter>',
                0,
                '<meter value="0.6">60%</meter>',
                0,
                '<meter value="0.6">60%</meter>',
                0
            ),
            'meter - cannot contain meter element' => array(
                '<meter value="0.6">60%<meter value="0.5">50%</meter></meter>',
                '<meter value="0.6">60%<meter value="0.5">50%</meter></meter>',
                0,
                '<meter value="0.6">60%<meter value="0.5">50%</meter></meter>',
                0,
                '<meter value="0.6">60%</meter>',
                1,
                '<meter value="0.6">60%</meter>',
                1
            ),
            'object - correct usage' => array(
                '<object type="application/vnd.o3d.auto">
     <param name="o3d_features" value="FloatingPointTextures">
     <img src="o3d-teapot.png"
          title="3D Utah Teapot illustration rendered using O3D."
          alt="Utah Teapot">
     <p>To see the teapot actually rendered by O3D on your
     computer, please download and install the <a
     href="http://code.google.com/apis/o3d/docs/gettingstarted.html#install">O3D plugin</a>.</p>
    </object>',
                '<object type="application/vnd.o3d.auto"><param name="o3d_features" value="FloatingPointTextures"/><img src="o3d-teapot.png" title="3D Utah Teapot illustration rendered using O3D." alt="Utah Teapot"/><p>To see the teapot actually rendered by O3D on your computer, please download and install the <a href="http://code.google.com/apis/o3d/docs/gettingstarted.html#install">O3D plugin</a>.</p></object>',
                0,
                '<object type="application/vnd.o3d.auto"><param name="o3d_features" value="FloatingPointTextures"/><img src="o3d-teapot.png" title="3D Utah Teapot illustration rendered using O3D." alt="Utah Teapot"/><p>To see the teapot actually rendered by O3D on your computer, please download and install the <a href="http://code.google.com/apis/o3d/docs/gettingstarted.html#install">O3D plugin</a>.</p></object>',
                0,
                '<object type="application/vnd.o3d.auto"><param name="o3d_features" value="FloatingPointTextures"/><img src="o3d-teapot.png" title="3D Utah Teapot illustration rendered using O3D." alt="Utah Teapot"/><p>To see the teapot actually rendered by O3D on your computer, please download and install the <a href="http://code.google.com/apis/o3d/docs/gettingstarted.html#install">O3D plugin</a>.</p></object>',
                0,
                '<object type="application/vnd.o3d.auto"><param name="o3d_features" value="FloatingPointTextures"/><img src="o3d-teapot.png" title="3D Utah Teapot illustration rendered using O3D." alt="Utah Teapot"/><p>To see the teapot actually rendered by O3D on your computer, please download and install the <a href="http://code.google.com/apis/o3d/docs/gettingstarted.html#install">O3D plugin</a>.</p></object>',
                0
            ),
            'ol - li[value] children' => array(
                '<ol><li value=1>asdf1</li><li value=3><data value="8">Eight</data></li></ol>',
                '<ol><li value="1">asdf1</li><li value="3"><data value="8">Eight</data></li></ol>',
                0,
                '<ol><li value="1">asdf1</li><li value="3"><data value="8">Eight</data></li></ol>',
                0,
                '<ol><li value="1">asdf1</li><li value="3"><data value="8">Eight</data></li></ol>',
                0,
                '<ol><li value="1">asdf1</li><li value="3"><data value="8">Eight</data></li></ol>',
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
            'optgroup - correct usage' => array(
                '<select>
  <optgroup label="Swedish Cars">
    <option value="volvo">Volvo</option>
    <option value="saab">Saab</option>
  </optgroup>
  <optgroup label="German Cars">
    <option value="mercedes">Mercedes</option>
    <option value="audi">Audi</option>
  </optgroup>
</select>',
                '<select><optgroup label="Swedish Cars"><option value="volvo">Volvo</option><option value="saab">Saab</option></optgroup><optgroup label="German Cars"><option value="mercedes">Mercedes</option><option value="audi">Audi</option></optgroup></select>',
                0,
                '<select><optgroup label="Swedish Cars"><option value="volvo">Volvo</option><option value="saab">Saab</option></optgroup><optgroup label="German Cars"><option value="mercedes">Mercedes</option><option value="audi">Audi</option></optgroup></select>',
                0,
                '<select><optgroup label="Swedish Cars"><option value="volvo">Volvo</option><option value="saab">Saab</option></optgroup><optgroup label="German Cars"><option value="mercedes">Mercedes</option><option value="audi">Audi</option></optgroup></select>',
                0,
                '<select><optgroup label="Swedish Cars"><option value="volvo">Volvo</option><option value="saab">Saab</option></optgroup><optgroup label="German Cars"><option value="mercedes">Mercedes</option><option value="audi">Audi</option></optgroup></select>',
                0
            ),
            'optgroup - invalid parents' => array(
                '<div><optgroup label="Swedish Cars"><option value="volvo">Volvo</option><option value="saab">Saab</option></optgroup><optgroup label="German Cars"><option value="mercedes">Mercedes</option><option value="audi">Audi</option></optgroup></div>',
                '<div><optgroup label="Swedish Cars"><option value="volvo">Volvo</option><option value="saab">Saab</option></optgroup><optgroup label="German Cars"><option value="mercedes">Mercedes</option><option value="audi">Audi</option></optgroup></div>',
                0,
                '<div><optgroup label="Swedish Cars"><option value="volvo">Volvo</option><option value="saab">Saab</option></optgroup><optgroup label="German Cars"><option value="mercedes">Mercedes</option><option value="audi">Audi</option></optgroup></div>',
                0,
                '<div></div>',
                2,
                '<div></div>',
                2
            ),
            'optgroup - invalid children' => array(
                '<select><optgroup label="asdf"><img src="../stuff.png"/></optgroup></select>',
                '<select><optgroup label="asdf"><img src="../stuff.png"/></optgroup></select>',
                0,
                '<select><optgroup label="asdf"><img src="../stuff.png"/></optgroup></select>',
                0,
                '<select><optgroup label="asdf"></optgroup></select>',
                1,
                '<select><optgroup label="asdf"></optgroup></select>',
                1
            ),
            'option and select - correct usage' => array(
                '<p><label for="unittype">Select unit type:</label><select id="unittype" name="unittype"><option label="Miner"> Miner </option><option value="2"> Puffer </option><option value="3" selected> Snipey </option><option label="Max" value="4"><!-- Max --></option></select></p>',
                '<p><label for="unittype">Select unit type:</label><select id="unittype" name="unittype"><option label="Miner"> Miner </option><option value="2"> Puffer </option><option value="3" selected> Snipey </option><option label="Max" value="4"><!-- Max --></option></select></p>',
                0,
                '<p><label for="unittype">Select unit type:</label><select id="unittype" name="unittype"><option label="Miner"> Miner </option><option value="2"> Puffer </option><option value="3" selected> Snipey </option><option label="Max" value="4"><!-- Max --></option></select></p>',
                0,
                '<p><label for="unittype">Select unit type:</label><select id="unittype" name="unittype"><option label="Miner"> Miner </option><option value="2"> Puffer </option><option value="3" selected> Snipey </option><option label="Max" value="4"><!-- Max --></option></select></p>',
                0,
                '<p><label for="unittype">Select unit type:</label><select id="unittype" name="unittype"><option label="Miner"> Miner </option><option value="2"> Puffer </option><option value="3" selected> Snipey </option><option label="Max" value="4"><!-- Max --></option></select></p>',
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
            'option - invalid children' => array(
                '<select><option value="1">Miner</option><option label="asdf" value="2"><!-- comment -->Asdf</option><option label="asdf2"><a href="../src.html">SRC</a></option><option>asdf<a href="../src.html">SRC</a></option></select>',
                '<select><option value="1">Miner</option><option label="asdf" value="2"><!-- comment -->Asdf</option><option label="asdf2"><a href="../src.html">SRC</a></option><option>asdf<a href="../src.html">SRC</a></option></select>',
                0,
                '<select><option value="1">Miner</option><option label="asdf" value="2"><!-- comment -->Asdf</option><option label="asdf2"><a href="../src.html">SRC</a></option><option>asdf<a href="../src.html">SRC</a></option></select>',
                0,
                '<select><option value="1">Miner</option><option label="asdf" value="2"><!-- comment --></option><option label="asdf2"></option><option>asdf</option></select>',
                3,
                '<select><option value="1">Miner</option><option label="asdf" value="2"><!-- comment --></option><option label="asdf2"></option><option>asdf</option></select>',
                3
            ),
            'param - invalid parent' => array(
                '<div>Hmmm...<param name="o3d_features" value="FloatingPointTextures"></div>',
                '<div>Hmmm...<param name="o3d_features" value="FloatingPointTextures"/></div>',
                0,
                '<div>Hmmm...<param name="o3d_features" value="FloatingPointTextures"/></div>',
                0,
                '<div>Hmmm...</div>',
                1,
                '<div>Hmmm...</div>',
                1
            ),
            'picture - correct usage' => array(
                '<picture>
    <source srcset="smaller.jpg" media="(max-width: 768px)">
    <source srcset="default.jpg">
    <img srcset="default.jpg" alt="My default image">
</picture>',
                '<picture><source srcset="smaller.jpg" media="(max-width: 768px)"/><source srcset="default.jpg"/><img srcset="default.jpg" alt="My default image"/></picture>',
                0,
                '<picture><source srcset="smaller.jpg" media="(max-width: 768px)"/><source srcset="default.jpg"/><img srcset="default.jpg" alt="My default image"/></picture>',
                0,
                '<picture><source srcset="smaller.jpg" media="(max-width: 768px)"/><source srcset="default.jpg"/><img srcset="default.jpg" alt="My default image"/></picture>',
                0,
                '<picture><source srcset="smaller.jpg" media="(max-width: 768px)"/><source srcset="default.jpg"/><img srcset="default.jpg" alt="My default image"/></picture>',
                0
            ),
            'picture - too many images' => array(
                '<picture><img srcset="default1.jpg" alt="My default image"/><img srcset="default2.jpg" alt="My default image"/><img srcset="default3.jpg" alt="My default image"/><embed /></picture>',
                '<picture><img srcset="default1.jpg" alt="My default image"/><img srcset="default2.jpg" alt="My default image"/><img srcset="default3.jpg" alt="My default image"/><embed/></picture>',
                0,
                '<picture><img srcset="default1.jpg" alt="My default image"/><img srcset="default2.jpg" alt="My default image"/><img srcset="default3.jpg" alt="My default image"/><embed/></picture>',
                0,
                '<picture><img srcset="default1.jpg" alt="My default image"/></picture>',
                3,
                '<picture><img srcset="default1.jpg" alt="My default image"/></picture>',
                3
            ),
            'progress - correct usage' => array(
                '<p>Progress: <progress id="p" max=100><span>0</span>%</progress></p>',
                '<p>Progress: <progress id="p" max="100"><span>0</span>%</progress></p>',
                0,
                '<p>Progress: <progress id="p" max="100"><span>0</span>%</progress></p>',
                0,
                '<p>Progress: <progress id="p" max="100"><span>0</span>%</progress></p>',
                0,
                '<p>Progress: <progress id="p" max="100"><span>0</span>%</progress></p>',
                0
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
            'select - invalid children' => array(
                '<select><option>asdf1</option><div>asdf2</div></select>',
                '<select><option>asdf1</option><div>asdf2</div></select>',
                0,
                '<select><option>asdf1</option><div>asdf2</div></select>',
                0,
                '<select><option>asdf1</option></select>',
                1,
                '<select><option>asdf1</option></select>',
                1
            ),
            'slot' => array(
                '<slot name="asdf">asdf</slot>',
                '<slot name="asdf">asdf</slot>',
                0,
                '<slot name="asdf">asdf</slot>',
                0,
                '<slot name="asdf">asdf</slot>',
                0,
                '<slot name="asdf">asdf</slot>',
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
                '<table><caption>Stuff</caption><thead><tr><th>Animal<dialog open>Hmmm...</dialog></th><th colspan=2>Color</th></tr></thead><!-- body --><tbody><tr><td>Ant</td><td colspan=2>Black</td></tr></tbody><tfoot><tr><td colspan=2>---</td><td>---</td></tr></tfoot></table>',
                '<table><caption>Stuff</caption><thead><tr><th>Animal<dialog open>Hmmm...</dialog></th><th colspan="2">Color</th></tr></thead><!-- body --><tbody><tr><td>Ant</td><td colspan="2">Black</td></tr></tbody><tfoot><tr><td colspan="2">---</td><td>---</td></tr></tfoot></table>',
                0,
                '<table><caption>Stuff</caption><thead><tr><th>Animal<dialog open>Hmmm...</dialog></th><th colspan="2">Color</th></tr></thead><!-- body --><tbody><tr><td>Ant</td><td colspan="2">Black</td></tr></tbody><tfoot><tr><td colspan="2">---</td><td>---</td></tr></tfoot></table>',
                0,
                '<table><caption>Stuff</caption><thead><tr><th>Animal<dialog open>Hmmm...</dialog></th><th colspan="2">Color</th></tr></thead><!-- body --><tbody><tr><td>Ant</td><td colspan="2">Black</td></tr></tbody><tfoot><tr><td colspan="2">---</td><td>---</td></tr></tfoot></table>',
                0,
                '<table><caption>Stuff</caption><thead><tr><th>Animal<dialog open>Hmmm...</dialog></th><th colspan="2">Color</th></tr></thead><!-- body --><tbody><tr><td>Ant</td><td colspan="2">Black</td></tr></tbody><tfoot><tr><td colspan="2">---</td><td>---</td></tr></tfoot></table>',
                0
            ),
            'table - correct usage with colgroup' => array(
                '<table>
  <colgroup span=3>
    <col span="2" style="background-color:red">
    <col style="background-color:yellow">
  </colgroup>
  <tr>
    <th>ISBN</th>
    <th>Title</th>
    <th>Price</th>
  </tr>
  <tr>
    <td>3476896</td>
    <td>My first HTML</td>
    <td>$53</td>
  </tr>
</table>',
                '<table><colgroup span="3"><col span="2" style="background-color:red"/><col style="background-color:yellow"/></colgroup><tr><th>ISBN</th><th>Title</th><th>Price</th></tr><tr><td>3476896</td><td>My first HTML</td><td>$53</td></tr></table>',
                0,
                '<table><colgroup span="3"><col span="2" style="background-color:red"/><col style="background-color:yellow"/></colgroup><tr><th>ISBN</th><th>Title</th><th>Price</th></tr><tr><td>3476896</td><td>My first HTML</td><td>$53</td></tr></table>',
                0,
                '<table><colgroup span="3"><col span="2" style="background-color:red"/><col style="background-color:yellow"/></colgroup><tr><th>ISBN</th><th>Title</th><th>Price</th></tr><tr><td>3476896</td><td>My first HTML</td><td>$53</td></tr></table>',
                0,
                '<table><colgroup span="3"><col span="2" style="background-color:red"/><col style="background-color:yellow"/></colgroup><tr><th>ISBN</th><th>Title</th><th>Price</th></tr><tr><td>3476896</td><td>My first HTML</td><td>$53</td></tr></table>',
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
            'time - correct usage' => array(
                'At the tone, the time will be <time class="time">10:00</time>.',
                'At the tone, the time will be <time class="time">10:00</time>.',
                0,
                'At the tone, the time will be <time class="time">10:00</time>.',
                0,
                'At the tone, the time will be <time class="time">10:00</time>.',
                0,
                'At the tone, the time will be <time class="time">10:00</time>.',
                0
            ),
            'time - incorrect children' => array(
                '<time><!-- comment --><a href="time.html">10:00</a></time>.',
                '<time><!-- comment --><a href="time.html">10:00</a></time>.',
                0,
                '<time><!-- comment --><a href="time.html">10:00</a></time>.',
                0,
                '<time><!-- comment --></time>.',
                1,
                '<time><!-- comment --></time>.',
                1
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
            'track - incorrect parents' => array(
                '<div><track src="asdf.mp4"/></div>',
                '<div><track src="asdf.mp4"/></div>',
                0,
                '<div><track src="asdf.mp4"/></div>',
                0,
                '<div></div>',
                1,
                '<div></div>',
                1
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
    <a href="http://www.example.com">Intro</a>
    <track kind=subtitles src=brave.en.vtt srclang=en label="English">
    <track kind=captions src=brave.en.hoh.vtt srclang=en label="English for the Hard of Hearing">
</video>',
                '<video src="brave.webm"><a href="http://www.example.com">Intro</a><track kind="subtitles" src="brave.en.vtt" srclang="en" label="English"/><track kind="captions" src="brave.en.hoh.vtt" srclang="en" label="English for the Hard of Hearing"/></video>',
                0,
                '<video src="brave.webm"><a href="http://www.example.com">Intro</a><track kind="subtitles" src="brave.en.vtt" srclang="en" label="English"/><track kind="captions" src="brave.en.hoh.vtt" srclang="en" label="English for the Hard of Hearing"/></video>',
                0,
                '<video src="brave.webm"><a href="http://www.example.com">Intro</a><track kind="subtitles" src="brave.en.vtt" srclang="en" label="English"/><track kind="captions" src="brave.en.hoh.vtt" srclang="en" label="English for the Hard of Hearing"/></video>',
                0,
                '<video src="brave.webm"><a href="http://www.example.com">Intro</a><track kind="subtitles" src="brave.en.vtt" srclang="en" label="English"/><track kind="captions" src="brave.en.hoh.vtt" srclang="en" label="English for the Hard of Hearing"/></video>',
                0
            ),
            'video - correct usage with source' => array(
                '<video><source type="yo">brave.mp4</video>',
                '<video><source type="yo"/>brave.mp4</video>',
                0,
                '<video><source type="yo"/>brave.mp4</video>',
                0,
                '<video><source type="yo"/>brave.mp4</video>',
                0,
                '<video><source type="yo"/>brave.mp4</video>',
                0
            ),
            'video - incorrect children' => array(
                '<video><div>brave.mp4</div></video>',
                '<video><div>brave.mp4</div></video>',
                0,
                '<video><div>brave.mp4</div></video>',
                0,
                '<video></video>',
                1,
                '<video></video>',
                1
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
