<?php

namespace Groundskeeper\Tokens\Elements;

class Base extends ClosedElement
{
    protected function getAllowedAttrbutes()
    {
        $baseAllowedAttributes = array(
            '/^href$/i' => Element::ATTR_URI,
            '/^target$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $baseAllowedAttributes,
            parent::getAllowedAttrbutes()
        );
    }
}
