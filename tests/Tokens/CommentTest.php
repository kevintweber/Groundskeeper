<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Comment;

class CommentTest extends \PHPUnit_Framework_TestCase
{
    public function testComment()
    {
        $configuration = new Configuration(array(
            'type-blacklist' => ''
        ));
        $comment = new Comment($configuration, 'asdf');
        $this->assertEquals('asdf', $comment->getValue());
        $this->assertEquals(
            '<!-- asdf -->',
            $comment->toHtml('', '')
        );
        $comment->setValue('qwerty');
        $this->assertEquals(
            '<!-- qwerty -->',
            $comment->toHtml('', '')
        );
    }
}
