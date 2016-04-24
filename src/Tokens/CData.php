<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

class CData extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct($parent = null, $value = null)
    {
        parent::__construct(Token::CDATA, $parent, $value);
    }

    public function toString(Configuration $configuration, $prefix = '', $suffix = '')
    {
        if (!$this->isValid) {
            return '';
        }

        return $prefix . '<![CDATA[' . $this->getValue() . ']]>' . $suffix;
    }
}
