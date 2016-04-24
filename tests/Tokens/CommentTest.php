<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Comment;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    public function testComment()
    {
        $comment = new Comment(null, 'asdf');
        $this->assertEquals('asdf', $comment->getValue());
        $comment->setIsValid(true);
        $configuration = new Configuration();
        $this->assertEquals(
            '<!-- asdf -->',
            $comment->toString($configuration)
        );

        $comment->setIsValid(false);
        $this->assertEquals(
            '',
            $comment->toString($configuration)
        );
    }
}
