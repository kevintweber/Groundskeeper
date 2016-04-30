<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;

class Style extends OpenElement implements MetadataContent
{
    protected function getAllowedAttributes()
    {
        $styleAllowedAttributes = array(
            '/^media$/i' => Element::ATTR_CI_STRING,
            '/^nonce$/i' => Element::ATTR_CS_STRING,
            '/^type$/i' => Element::ATTR_CI_STRING,
            '/^scoped$/i' => Element::ATTR_CI_STRING
        );

        return array_merge(
            $styleAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
