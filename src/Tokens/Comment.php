<?php

namespace Groundskeeper\Tokens;

/**
 * Commend token type.
 */
class Comment extends AbstractValuedToken implements NonParticipating
{
    public function getType() : string
    {
        return Token::COMMENT;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml(string $prefix, string $suffix) : string
    {
        return $prefix . '<!-- ' . $this->getValue() . ' -->' . $suffix;
    }
}
