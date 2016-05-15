<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InlineElement;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "data" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-data-element
 */
class Data extends OpenElement implements FlowContent, PhrasingContent, InlineElement
{
    protected function getAllowedAttributes()
    {
        $dataAllowedAttributes = array(
            '/^value$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $dataAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
