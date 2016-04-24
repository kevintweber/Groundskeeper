<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

class Comment extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, Token $parent = null, $value = null)
    {
        parent::__construct(Token::COMMENT, $configuration, $parent, $value);
    }

    protected function buildHtml($prefix, $suffix)
    {
        return $prefix . '<!-- ' . $this->getValue() . ' -->' . $suffix;
    }
}
