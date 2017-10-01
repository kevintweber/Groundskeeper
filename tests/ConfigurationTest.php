<?php

namespace Groundskeeper\Tests;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
use PHPUnit\Framework\TestCase;

class ConfigurationTest extends TestCase
{
    public function testConstructorAndDefaults()
    {
        $configuration = new Configuration();
        $this->assertEquals('standard', $configuration->get('clean-strategy'));
        $this->assertEquals('', $configuration->get('element-blacklist'));
        $this->assertEquals(0, $configuration->get('indent-spaces'));
        $this->assertEquals('compact', $configuration->get('output'));
        $this->assertEquals('cdata,comment', $configuration->get('type-blacklist'));
    }

    /**
     * @dataProvider exceptionInConfigurationDataProvider
     * @expectedException Symfony\Component\OptionsResolver\Exception\InvalidOptionsException
     */
    public function testExceptionInConfiguration($key, $value)
    {
        $configuration = new Configuration(
            array($key => $value)
        );
    }

    public function exceptionInConfigurationDataProvider()
    {
        return array(
            array('clean-strategy', 5),
            array('clean-strategy', 'asdf'),
            array('element-blacklist', 5),
            array('indent-spaces', -2),
            array('indent-spaces', 'asdf'),
            array('output', 'asdf'),
            array('output', 5),
            array('type-blacklist', 5),
            array('type-blacklist', 'asdf')
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testHasAndGet()
    {
        $configuration = new Configuration();
        $this->assertTrue($configuration->has('clean-strategy'));
        $this->assertEquals('standard', $configuration->get('clean-strategy'));
        $this->assertFalse($configuration->has('asdf'));
        $configuration->get('asdf');
    }

    public function testIsAllowedElement()
    {
        $configuration = new Configuration(array(
            'element-blacklist' => 'em,asdf'
        ));
        $this->assertTrue($configuration->isAllowedElement('div'));
        $this->assertFalse($configuration->isAllowedElement('em'));
        $asdf = new Element($configuration, 0, 0, 'asdf');
        $this->assertFalse($configuration->isAllowedElement($asdf));
    }

    public function testIsAllowedType()
    {
        $configuration = new Configuration();
        $asdf = new Element($configuration, 0, 0, 'asdf');
        $this->assertTrue($configuration->isAllowedType($asdf));
        $this->assertFalse($configuration->isAllowedType('comment'));
    }
}
