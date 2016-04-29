<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Token;

class ClosedElement extends Element
{
    /**
     * Reimplement appendChild so that no children can be added.
     */
    public function appendChild(Token $token)
    {
        return $this;
    }

    /**
     * Reimplement prependChild so that no children can be added.
     */
    public function prependChild(Token $token)
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
