<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

class CData extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, Token $parent = null, $value = null)
    {
        parent::__construct(Token::CDATA, $configuration, $parent, $value);
    }

    protected function buildHtml($prefix, $suffix)
    {
        return $prefix . '<![CDATA[' . $this->getValue() . ']]>' . $suffix;
    }
}
