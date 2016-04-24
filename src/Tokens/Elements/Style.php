<?php

namespace Groundskeeper\Tokens\Elements;

class Style extends OpenElement
{
    protected function getAllowedAttrbutes()
    {
        $styleAllowedAttributes = array(
            '/^media$/i' => Element::ATTR_CI_STRING,
            '/^nonce$/i' => Element::ATTR_CS_STRING,
            '/^type$/i' => Element::ATTR_CI_STRING,
            '/^scoped$/i' => Element::ATTR_CI_STRING
        );

        return array_merge(
            $styleAllowedAttributes,
            parent::getAllowedAttrbutes()
        );
    }
}
