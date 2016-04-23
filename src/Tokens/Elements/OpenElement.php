<?php

namespace Groundskeeper\Tokens\Elements;

class OpenElement extends Element
{
    public function toString($prefix = '', $suffix = '')
    {
        $output = $prefix . '<' . $this->name;
        foreach ($this->attributes as $key => $value) {
            $output .= ' ' . $key;
            if (is_string($value)) {
                $output .= '="' . $value . '"';
            }
        }

        return $output . '>' . $suffix;
    }
}
