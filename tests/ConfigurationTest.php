<?php

namespace Groundskeeper\Tests;

use Groundskeeper\Configuration;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $configuration = new Configuration();
        $this->assertEquals('standard', $configuration->get('clean-strategy'));
        $this->assertEquals('fix', $configuration->get('error-strategy'));
        $this->assertEquals(0, $configuration->get('indent-spaces'));
        $this->assertEquals('compact', $configuration->get('output'));
        $this->assertEquals('cdata,comment', $configuration->get('remove-types'));
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
            array('error-strategy', 5),
            array('error-strategy', 'asdf'),
            array('indent-spaces', -2),
            array('indent-spaces', 'asdf'),
            array('output', 'asdf'),
            array('output', 5),
            array('remove-types', 5),
            array('remove-types', 'asdf')
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

    public function testIsAllowedType()
    {
        $configuration = new Configuration();
        $this->assertTrue($configuration->isAllowedType('element'));
        $this->assertFalse($configuration->isAllowedType('comment'));
    }
}
