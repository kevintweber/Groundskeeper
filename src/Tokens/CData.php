<?php

namespace Groundskeeper\Tokens;

class CData extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Token $parent = null, $value = null)
    {
        parent::__construct(Token::CDATA, $parent, $value);
    }

    public function toString($prefix = '', $suffix = '')
    {
        return $prefix . '<![CDATA[' . $this->getValue() . ']]>' . $suffix;
    }
}
