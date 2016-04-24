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
    public function testClean($html, $expectedOutput)
    {
        $groundskeeper = new Groundskeeper(array('remove-types' => 'none'));
        $groundskeeper->setLogger(new NullLogger());
        $this->assertEquals(
            $expectedOutput,
            $groundskeeper->clean($html)
        );
    }

    public function cleanDataProvider()
    {
        return array(
            'cdata only' => array(
                '<![CDATA[asdf]]>',
                '<![CDATA[asdf]]>'
            ),
            'cdata with whitespace' => array(
                '     <![CDATA[asdf]]>      ',
                '<![CDATA[asdf]]>'
            ),
            'comment only' => array(
                '<!-- asdf -->',
                '<!-- asdf -->'
            ),
            'comment with whitespace' => array(
                '     <!-- asdf -->      ',
                '<!-- asdf -->'
            ),
            'doctype only' => array(
                '<!DOCTYPE asdf>',
                '<!DOCTYPE asdf>'
            ),
            'doctype with whitespace' => array(
                '     <!DOCTYPE asdf>      ',
                '<!DOCTYPE asdf>'
            ),
            'element only' => array(
                '<asdf/>',
                '<asdf/>'
            ),
            'element with whitespace' => array(
                '     <asdf/>      ',
                '<asdf/>'
            ),
            'text only' => array(
                'asdf',
                'asdf'
            ),
            'text with whitespace' => array(
                '     asdf      ',
                'asdf'
            ),
            'element with child' => array(
                '<asdf1><asdf2/></asdf1>',
                '<asdf1><asdf2/></asdf1>'
            ),
            'element with child and whitespace' => array(
                '    <asdf1>           <asdf2/>          </asdf1>           ',
                '<asdf1><asdf2/></asdf1>'
            ),
            'element with child and attributes' => array(
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3"><asdf2 class="asdf4"/></asdf1>'
            ),
            'element with child and valid and invalid attributes' => array(
                '<asdf1 id="asdf3"   asdf5="asdf6">Text goes here<asdf2 class="asdf4"/></asdf1>',
                '<asdf1 id="asdf3">Text goes here<asdf2 class="asdf4"/></asdf1>'
            ),
            'element with case insensitive attribute values' => array(
                '<asdf role=ASDF1>Text goes here</asdf>',
                '<asdf role="asdf1">Text goes here</asdf>'
            ),
        );
    }
}
