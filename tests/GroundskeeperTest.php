<?php

namespace Groundskeeper\Tests;

use Groundskeeper\Groundskeeper;

class GroundskeeperTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $groundskeeper = new Groundskeeper();
        $options = $groundskeeper->getOptions();
        $this->assertEquals(4, $options['indent-spaces']);
        $this->assertEquals('compact', $options['output']);
        $this->assertFalse($options['throw-on-error']);
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
}
