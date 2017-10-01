<?php

namespace Groundskeeper\Tokens\ElementTypes;

use Groundskeeper\Tokens\Element;

abstract class OpenElement extends Element
{
    /**
     * Required by the Token interface.
     */
    public function toHtml(string $prefix, string $suffix) : string
    {
        $output = $this->buildStartTag($prefix, $suffix, true);
        $output .= $this->buildChildrenHtml($prefix, $suffix);

        return $output . $prefix . '</' . $this->getName() . '>' . $suffix;
    }
}
