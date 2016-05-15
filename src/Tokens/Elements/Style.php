<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;

class Style extends OpenElement implements MetadataContent
{
    protected function getAllowedAttributes()
    {
        $styleAllowedAttributes = array(
            '/^media$/i' => Attribute::CS_STRING,
            '/^nonce$/i' => Attribute::CS_STRING,
            '/^type$/i' => Attribute::CI_STRING,
            '/^scoped$/i' => Attribute::CI_STRING
        );

        return array_merge(
            $styleAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
