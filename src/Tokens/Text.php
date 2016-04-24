<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

class Text extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, Token $parent = null, $value = null)
    {
        parent::__construct(Token::TEXT, $configuration, $parent, $value);
    }

    protected function buildHtml($prefix, $suffix)
    {
        return $prefix . $this->getValue() . $suffix;
    }
}
