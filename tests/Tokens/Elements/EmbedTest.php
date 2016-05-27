<?php

namespace Groundskeeper\Tests\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Elements\Embed;

class EmbedTest extends \PHPUnit_Framework_TestCase
{
    public function testIsInteractiveContent()
    {
        $configuration = new Configuration();
        $d = new Embed($configuration, 0, 0, 'embed');
        $this->assertTrue($d->isInteractiveContent());
    }
}
