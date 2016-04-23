<?php

namespace Groundskeeper\Tokens\Elements;

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

    public function toString($prefix = '', $suffix = '')
    {
        $output = $prefix . '<' . $this->name;
        foreach ($this->attributes as $key => $value) {
            $output .= ' ' . $key;
            if (is_string($value)) {
                $output .= '="' . $value . '"';
            }
        }

        return $output . '/>' . $suffix;
    }
}
