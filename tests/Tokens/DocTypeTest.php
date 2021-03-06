<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tests\TestableLogger;
use Groundskeeper\Tokens\DocType;
use PHPUnit\Framework\TestCase;

class DocTypeTest extends TestCase
{
    public function testDocType()
    {
        $configuration = new Configuration(array(
            'type-blacklist' => ''
        ));
        $docType = new DocType($configuration, 0, 0, 'asdf');
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
        $docType = new DocType($configuration, 0, 0, 'asdf');
        $logger = new TestableLogger();
        $docType->clean($logger);
        $this->assertEquals(
            '<!DOCTYPE asdf>',
            $docType->toHtml('', '')
        );
    }
}
