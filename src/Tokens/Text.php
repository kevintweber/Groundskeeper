<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

class Text extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct($parent = null, $value = null)
    {
        parent::__construct(Token::TEXT, $parent, $value);
    }

    public function toString(Configuration $configuration, $prefix = '', $suffix = '')
    {
        if (!$this->isValid) {
            return '';
        }

        return $prefix . $this->getValue() . $suffix;
    }
}
