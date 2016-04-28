<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Token;

class ClosedElement extends Element
{
    /**
     * Reimplement addChild so that no children can be added.
     */
    public function addChild(Token $token)
    {
        return $this;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        return $this->buildStartTag($prefix, $suffix);
    }
}
