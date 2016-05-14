<?php

namespace Groundskeeper\Tokens;

/**
 * Commend token type.
 */
class Comment extends AbstractValuedToken implements NonParticipating
{
    public function getType()
    {
        return Token::COMMENT;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        return $prefix . '<!-- ' . $this->getValue() . ' -->' . $suffix;
    }
}
