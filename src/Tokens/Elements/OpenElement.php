<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;

class OpenElement extends Element
{
    public function toString(Configuration $configuration, $prefix = '', $suffix = '')
    {
        if (!$this->isValid) {
            return '';
        }

        $output = $this->toStringTag($configuration, $prefix, $suffix, true);
        foreach ($this->children as $child) {
            $newPrefix = $prefix . str_repeat(' ', $configuration->get('indent-spaces'));
            $output .= $child->toString($options, $newPrefix, $suffix);
        }

        return $output . $prefix . '</' . $this->name . '>' . $suffix;
    }
}
