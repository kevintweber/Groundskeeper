<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;

class OpenElement extends Element
{
    public function toString(Configuration $configuration, $prefix = '', $suffix = '')
    {
        if (!$this->isValid && $configuration->get('clean-strategy') != 'none') {
            return '';
        }

        $output = $this->toStringTag($configuration, $prefix, $suffix, true);
        foreach ($this->getChildren() as $child) {
            $newPrefix = $prefix . str_repeat(' ', $configuration->get('indent-spaces'));
            $output .= $child->toString($configuration, $newPrefix, $suffix);
        }

        return $output . $prefix . '</' . $this->getName() . '>' . $suffix;
    }
}
