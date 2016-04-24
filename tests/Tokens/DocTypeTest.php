<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\DocType;

class DocTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDocType()
    {
        $docType = new DocType(null, 'asdf');
        $this->assertEquals('asdf', $docType->getValue());
        $docType->setIsValid(true);
        $configuration = new Configuration();
        $this->assertEquals(
            '<!DOCTYPE asdf>',
            $docType->toString($configuration)
        );

        $docType->setIsValid(false);
        $this->assertEquals(
            '',
            $docType->toString($configuration)
        );
    }
}
