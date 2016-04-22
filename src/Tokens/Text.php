<?php

namespace Groundskeeper\Tokens;

class Text extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Token $parent = null, $value = null)
    {
        parent::__construct(Token::TEXT, $parent, $value);
    }

    public function toString($prefix = '', $suffix = '')
    {
        return $prefix . $this->getValue() . $suffix;
    }
}
