<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InlineElement;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\TransparentElement;

/**
 * "ins" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-ins-element
 */
class Ins extends OpenElement implements FlowContent, PhrasingContent, InlineElement, TransparentElement
{
    protected function getAllowedAttributes()
    {
        $insAllowedAttributes = array(
            '/^cite$/i' => Attribute::URI,
            '/^datetime$/i' => Attribute::CS_STRING
       );

        return array_merge(
            $insAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function isTransparentElement() : bool
    {
        return true;
    }
}
