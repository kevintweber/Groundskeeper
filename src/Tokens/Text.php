<?php

namespace Groundskeeper\Tokens;

class Text extends AbstractValuedToken
{
    public function getType() : string
    {
        return Token::TEXT;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml(string $prefix, string $suffix) : string
    {
        $text = preg_replace("/\s+/", ' ', $this->getValue());

        return $prefix . $text . $suffix;
    }
}
