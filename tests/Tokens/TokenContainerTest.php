<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Comment;
use Groundskeeper\Tokens\DocType;
use Groundskeeper\Tokens\Elements\Element;
use Groundskeeper\Tokens\TokenContainer;
use Psr\Log\NullLogger;

class TokenContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructorAndDefaults()
    {
        $configuration = new Configuration();
        $tokenContainer = new TokenContainer($configuration);
        $this->assertEmpty($tokenContainer->getChildren());
    }

    public function testChildren()
    {
        $configuration = new Configuration();
        $tokenContainer = new TokenContainer($configuration);
        $token = new Comment($configuration, 'asdf');
        $anotherToken = new Comment($configuration, 'qwerty');
        $this->assertEmpty($tokenContainer->getChildren());
        $this->assertFalse($tokenContainer->hasChild($token));
        $tokenContainer->appendChild($token);
        $this->assertEquals(
            array($token),
            $tokenContainer->getChildren()
        );
        $this->assertTrue($tokenContainer->hasChild($token));
        $tokenContainer->prependChild($anotherToken);
        $this->assertEquals(
            array($anotherToken, $token),
            $tokenContainer->getChildren()
        );
        $this->assertTrue($tokenContainer->removeChild($token));
        $this->assertEquals(
            array($anotherToken),
            $tokenContainer->getChildren()
        );
        $this->assertFalse($tokenContainer->removeChild($token));
    }

    public function testCleanWithNoCleanStrategy()
    {
        $configuration = new Configuration(array(
            'clean-strategy' => 'none'
        ));
        $tokenContainer = new TokenContainer($configuration);
        $token = new Comment($configuration, 'asdf');
        $tokenContainer->appendChild($token);
        $tokenContainer->clean(new NullLogger());
        $this->assertEquals(
            array($token),
            $tokenContainer->getChildren()
        );
    }
}
