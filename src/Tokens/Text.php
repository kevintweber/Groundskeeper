<?php

namespace Groundskeeper\Tokens;

class Text extends AbstractValuedToken
{
    public function getType()
    {
        return Token::TEXT;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        return $prefix . $this->getValue() . $suffix;
    }
}
