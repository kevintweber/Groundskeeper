<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tests\TestableLogger;
use Groundskeeper\Tokens\DocType;

class DocTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDocType()
    {
        $configuration = new Configuration(array(
            'type-blacklist' => ''
        ));
        $docType = new DocType($configuration, 'asdf');
        $this->assertEquals('asdf', $docType->getValue());
        $this->assertEquals(
            '<!DOCTYPE asdf>',
            $docType->toHtml('', '')
        );
    }

    public function testClean()
    {
        $configuration = new Configuration(array(
            'clean-strategy' => 'none',
            'type-blacklist' => ''
        ));
        $docType = new DocType($configuration, 'asdf');
        $logger = new TestableLogger();
        $docType->clean($logger);
        $this->assertEquals(
            '<!DOCTYPE asdf>',
            $docType->toHtml('', '')
        );
    }
}
