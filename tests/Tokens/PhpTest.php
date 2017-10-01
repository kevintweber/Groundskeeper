<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Php;
use PHPUnit\Framework\TestCase;

class PhpTest extends TestCase
{
    public function testPhp()
    {
        $configuration = new Configuration(array(
            'type-blacklist' => ''
        ));
        $php = new Php($configuration, 0, 0, 'echo "asdf";');
        $this->assertEquals('echo "asdf";', $php->getValue());
        $this->assertEquals(
            '<?php echo "asdf"; ?>',
            $php->toHtml('', '')
        );
    }
}
