<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;

class ClosedElement extends Element
{
    public function getChildren()
    {
        return array();
    }

    public function addChild(Token $token)
    {
        return $this;
    }

    public function removeChild(Token $token)
    {
        return true;
    }

    public function toString(Configuration $configuration, $prefix = '', $suffix = '')
    {
        if (!$this->isValid && $configuration->get('clean-strategy') != 'none') {
            return '';
        }

        return $this->toStringTag($configuration, $prefix, $suffix);
    }
}
