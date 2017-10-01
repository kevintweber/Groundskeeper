<?php

namespace Groundskeeper\Tests\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Comment;
use PHPUnit\Framework\TestCase;

class CommentTest extends TestCase
{
    public function testComment()
    {
        $configuration = new Configuration(array(
            'type-blacklist' => ''
        ));
        $comment = new Comment($configuration, 0, 0, 'asdf');
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
