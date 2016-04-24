<?php

namespace Groundskeeper\Tokens\Elements;

class Meta extends ClosedElement
{
    protected function getAllowedAttributes()
    {
        $metaAllowedAttributes = array(
            '/^name$/i' => Element::ATTR_CS_STRING,
            '/^http-equiv$/i' => Element::ATTR_CS_STRING,
            '/^content$/i' => Element::ATTR_CS_STRING,
            '/^charset$/i' => Element::ATTR_CS_STRING,
            '/^property$/i' => Element::ATTR_CS_STRING  // Facebook OG attribute name.
        );

        return array_merge(
            $metaAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
