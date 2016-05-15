<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InlineElement;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "q" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-q-element
 */
class Q extends OpenElement implements FlowContent, PhrasingContent, InlineElement
{
    protected function getAllowedAttributes()
    {
        $aAllowedAttributes = array(
            '/^cite$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $aAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
