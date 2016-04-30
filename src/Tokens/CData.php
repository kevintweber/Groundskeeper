<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

class CData extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, $value = null)
    {
        parent::__construct(Token::CDATA, $configuration, $value);
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        return $prefix . '<![CDATA[' . $this->getValue() . ']]>' . $suffix;
    }
}
