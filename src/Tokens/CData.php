<?php

namespace Groundskeeper\Tokens;

class CData extends AbstractValuedToken
{
    public function getType() : string
    {
        return Token::CDATA;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml(string $prefix, string $suffix) : string
    {
        return $prefix . '<![CDATA[' . $this->getValue() . ']]>' . $suffix;
    }
}
