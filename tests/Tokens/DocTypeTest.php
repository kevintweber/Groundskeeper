<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
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
}
