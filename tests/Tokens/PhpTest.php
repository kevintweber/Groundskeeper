<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Php;

class PhpTest extends \PHPUnit_Framework_TestCase
{
    public function testPhp()
    {
        $configuration = new Configuration(array(
            'type-blacklist' => ''
        ));
        $php = new Php($configuration, 'echo "asdf";');
        $this->assertEquals('echo "asdf";', $php->getValue());
        $this->assertEquals(
            '<?php echo "asdf"; ?>',
            $php->toHtml('', '')
        );
    }
}
