<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;

class OpenElement extends Element
{
    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        $output = $this->buildStartTag($prefix, $suffix, true);
        $output .= $this->buildChildrenHtml($prefix, $suffix);

        return $output . $prefix . '</' . $this->getName() . '>' . $suffix;
    }
}
