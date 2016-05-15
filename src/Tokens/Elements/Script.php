<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
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
            '/^src$/i' => Attribute::URI,
            '/^type$/i' => Attribute::CS_STRING,
            '/^charset$/i' => Attribute::CS_STRING,
            '/^async$/i' => Attribute::BOOL,
            '/^defer$/i' => Attribute::BOOL,
            '/^crossorigin$/i' => Attribute::CS_STRING,
            '/^nonce$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $scriptAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
