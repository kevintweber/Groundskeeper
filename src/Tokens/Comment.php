<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

class Comment extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct($parent = null, $value = null)
    {
        parent::__construct(Token::COMMENT, $parent, $value);
    }

    public function toString(Configuration $configuration, $prefix = '', $suffix = '')
    {
        if (!$this->isValid) {
            return '';
        }

        return $prefix . '<!-- ' . $this->getValue() . ' -->' . $suffix;
    }
}
