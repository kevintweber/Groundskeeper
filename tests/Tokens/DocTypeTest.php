<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\DocType;

class DocTypeTest extends \PHPUnit_Framework_TestCase
{
    public function testDocType()
    {
        $configuration = new Configuration(array(
            'remove-types' => 'none'
        ));
        $docType = new DocType($configuration, null, 'asdf');
        $this->assertEquals('asdf', $docType->getValue());
        $this->assertEquals(
            '<!DOCTYPE asdf>',
            $docType->toHtml('', '')
        );
    }

    public function testDocTypeIsRemovedType()
    {
        $configuration = new Configuration(array(
            'remove-types' => 'doctype'
        ));
        $docType = new DocType($configuration, null, 'asdf');
        $this->assertEquals(
            '',
            $docType->toHtml('', '')
        );
    }
}
