<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InlineElement;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "del" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-del-element
 */
class Del extends OpenElement implements FlowContent, PhrasingContent, InlineElement
{
    protected function getAllowedAttributes()
    {
        $delAllowedAttributes = array(
            '/^cite$/i' => Element::ATTR_URI,
            '/^datetime$/i' => Element::ATTR_CS_STRING
       );

        return array_merge(
            $delAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
