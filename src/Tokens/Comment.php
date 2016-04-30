<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

/**
 * Commend token type.
 */
class Comment extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, Token $parent = null, $value = null)
    {
        parent::__construct(Token::COMMENT, $configuration, $parent, $value);
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        return $prefix . '<!-- ' . $this->getValue() . ' -->' . $suffix;
    }
}
