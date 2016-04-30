<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;

class Meta extends ClosedElement implements MetadataContent
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
