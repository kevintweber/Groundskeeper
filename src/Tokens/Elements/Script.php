<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;

class Script extends OpenElement implements FlowContent, MetadataContent, PhrasingContent, ScriptSupporting
{
    protected function getAllowedAttributes()
    {
        $scriptAllowedAttributes = array(
            '/^src$/i' => Element::ATTR_URI,
            '/^type$/i' => Element::ATTR_CS_STRING,
            '/^charset$/i' => Element::ATTR_CS_STRING,
            '/^async$/i' => Element::ATTR_BOOL,
            '/^defer$/i' => Element::ATTR_BOOL,
            '/^crossorigin$/i' => Element::ATTR_CS_STRING,
            '/^nonce$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $scriptAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
