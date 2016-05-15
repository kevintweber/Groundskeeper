<?php

namespace Groundskeeper\Tokens;

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
