<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tests\TestableLogger;
use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\Elements\A;
use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    public function testConstructorAndDefaults()
    {
        $attribute = new Attribute('class', 'test');
        $this->assertEquals('class', $attribute->getName());
        $this->assertNull($attribute->getType());
        $this->assertEquals('test', $attribute->getValue());
        $this->assertFalse($attribute->getIsStandard());
        $this->assertEquals('class="test"', (string) $attribute);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionInType()
    {
        $attribute = new Attribute('class', 'test');
        $attribute->setType(Attribute::BOOL);
        $this->assertEquals(Attribute::BOOL, $attribute->getType());
        $attribute->setType('asdf');
    }

    public function testIsStandard()
    {
        $attribute = new Attribute('class', 'test');
        $this->assertFalse($attribute->getIsStandard());
        $attribute->setIsStandard(true);
        $this->assertTrue($attribute->getIsStandard());
    }

    /**
     * @dataProvider cleanDataProvider
     */
    public function testClean($type, $value, $isStandard, $cleanStrategy, $expectedCleanResult, $expectedCleanValue, $debug = false)
    {
        $attribute = new Attribute('class', $value);
        $attribute->setIsStandard($isStandard);
        $attribute->setType($type);

        $configuration = new Configuration(
            array('clean-strategy' => $cleanStrategy)
        );
        $element = new A($configuration, 0, 0, 'a');
        $logger = new TestableLogger();

        $cleanResult = $attribute->clean($configuration, $element, $logger);
        if ($debug) {
            var_dump($logger->getLogs());
        }

        $this->assertSame(
            $expectedCleanResult,
            $cleanResult,
            'Testing clean return value.'
        );
        $this->assertEquals(
            $expectedCleanValue,
            $attribute->getValue(),
            'Testing modified value.'
        );
    }

    public function cleanDataProvider()
    {
        return array(
            'clean strategy - none' => array(
                Attribute::BOOL,
                'asdf',
                true,
                'none',
                true,
                'asdf'
            ),
            'lenient - keep non-standard' => array(
                Attribute::BOOL,
                'asdf',
                false,
                'lenient',
                true,
                true
            ),
            'standard - remove non-standard' => array(
                Attribute::BOOL,
                'asdf',
                false,
                'standard',
                false,
                'asdf'
            ),
            'boolean - correct' => array(
                Attribute::BOOL,
                true,
                true,
                'standard',
                true,
                true
            ),
            'boolean - incorrect' => array(
                Attribute::BOOL,
                'asfd',
                true,
                'standard',
                true,
                true
            ),
            'integer - correct' => array(
                Attribute::INT,
                '5',
                true,
                'standard',
                true,
                5
            ),
            'integer - reject non-numeric string' => array(
                Attribute::INT,
                'asfd',
                true,
                'standard',
                false,
                0
            ),
            'integer - reject true value' => array(
                Attribute::INT,
                true,
                true,
                'standard',
                false,
                true
            ),
            'integer - negative' => array(
                Attribute::INT,
                '-5',
                true,
                'standard',
                false,
                -5
            ),
            'ci-string - correct' => array(
                Attribute::CI_STRING,
                'ASDf',
                true,
                'standard',
                true,
                'asdf'
            ),
            'cs-string - correct' => array(
                Attribute::CS_STRING,
                'ASDf',
                true,
                'standard',
                true,
                'ASDf'
            ),
            'cs-string - correct' => array(
                Attribute::CS_STRING,
                true,
                true,
                'standard',
                true,
                'class'
            ),
            'uri - correct' => array(
                Attribute::URI,
                'http://www.example.com',
                true,
                'standard',
                true,
                'http://www.example.com'
            ),
            'uri - missing value' => array(
                Attribute::URI,
                true,
                true,
                'standard',
                false,
                true
            )
        );
    }
}
