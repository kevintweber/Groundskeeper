<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;

class OpenElement extends Element
{
    protected function buildHtml($prefix, $suffix)
    {
        $output = $this->buildStartTag($prefix, $suffix, true);
        $output .= $this->buildChildrenHtml($prefix, $suffix);

        return $output . $prefix . '</' . $this->getName() . '>' . $suffix;
    }
}
