<?php

namespace Groundskeeper\Tokens;

class DocType extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Token $parent = null, $value = null)
    {
        parent::__construct(Token::DOCTYPE, $parent, $value);
    }

    public function toString($prefix = '', $suffix = '')
    {
        return $prefix . '<!DOCTYPE ' . $this->getValue() . ' >' . $suffix;
    }
}
