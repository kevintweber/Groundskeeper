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
        $token = new Comment($configuration, null, 'asdf');
        $anotherToken = new Comment($configuration, null, 'qwerty');
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
        $token = new Comment($configuration, null, 'asdf');
        $tokenContainer->appendChild($token);
        $tokenContainer->clean(new NullLogger());
        $this->assertEquals(
            array($token),
            $tokenContainer->getChildren()
        );
    }

    /**
     * @expectedException Groundskeeper\Exceptions\ValidationException
     */
    public function testCleanWithThrowErrorStrategy()
    {
        $configuration = new Configuration(array(
            'error-strategy' => 'throw'
        ));
        $tokenContainer = new TokenContainer($configuration);
        $doctype = new Doctype($configuration, null, 'asdf');
        $div = new Element($configuration, 'div');
        $div->appendChild($doctype);
        $tokenContainer->appendChild($doctype);
        $tokenContainer->clean(new NullLogger());
    }

    public function testCleanWithFixErrorStrategy()
    {
        $configuration = new Configuration(array(
            'error-strategy' => 'fix'
        ));
        $tokenContainer = new TokenContainer($configuration);
        $doctype = new Doctype($configuration, null, 'asdf');
        $div = new Element($configuration, 'div');
        $div->appendChild($doctype);
        $tokenContainer->appendChild($doctype);
        $this->assertEquals(
            array($doctype),
            $tokenContainer->getChildren()
        );
        $tokenContainer->clean(new NullLogger());
        $this->assertEmpty($tokenContainer->getChildren());
    }
}
