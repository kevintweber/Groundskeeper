<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Tokens\Token;

class CData extends AbstractValuedToken
{
    public function getType()
    {
        return Token::CDATA;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        return $prefix . '<![CDATA[' . $this->getValue() . ']]>' . $suffix;
    }
}
