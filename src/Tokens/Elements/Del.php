<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InlineElement;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\TransparentElement;

/**
 * "del" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-del-element
 */
class Del extends OpenElement implements FlowContent, PhrasingContent, InlineElement, TransparentElement
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

    public function isTransparentElement()
    {
        return true;
    }
}
