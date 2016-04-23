<?php

namespace Groundskeeper\Tests;

use Groundskeeper\Groundskeeper;

class GroundskeeperTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $groundskeeper = new Groundskeeper();
        $configuration = $groundskeeper->getConfiguration();
        $this->assertEquals(0, $configuration->get('indent-spaces'));
        $this->assertEquals('compact', $configuration->get('output'));
        $this->assertFalse($configuration->get('throw-on-error'));
    }

    /**
     * @dataProvider exceptionInConfigurationDataProvider
     * @expectedException Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testExceptionInConfiguration($key, $value)
    {
        $groundskeeper = new Groundskeeper(
            array($key => $value)
        );
    }

    public function exceptionInConfigurationDataProvider()
    {
        return array(
            array('indent-spaces', -2),
            array('indent-spaces', 'asdf'),
            array('output', 'asdf'),
            array('output', 5),
            array('throw-on-error', 5)
        );
    }

    /**
     * @dataProvider cleanDataProvider
     */
    public function testClean($html, $expectedOutput)
    {
        $groundskeeper = new Groundskeeper();
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
            )
        );
    }
}
