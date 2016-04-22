<?php

namespace Groundskeeper\Tokens;

class Comment extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Token $parent = null, $value = null)
    {
        parent::__construct(Token::COMMENT, $parent, $value);
    }

    public function toString($prefix = '', $suffix = '')
    {
        return $prefix . '<!-- ' . $this->getValue() . ' -->' . $suffix;
    }
}
