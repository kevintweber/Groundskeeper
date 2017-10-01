<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\EmbeddedContent;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "object" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-object-element
 */
class Object extends OpenElement implements FlowContent, PhrasingContent, EmbeddedContent, InteractiveContent
{
    protected function getAllowedAttributes()
    {
        $objectAllowedAttributes = array(
            '/^data$/i' => Attribute::CS_STRING,
            '/^type$/i' => Attribute::CS_STRING,
            '/^typemustmatch$/i' => Attribute::CS_STRING,
            '/^name$/i' => Attribute::CS_STRING,
            '/^usemap$/i' => Attribute::CS_STRING,
            '/^form$/i' => Attribute::CS_STRING,
            '/^width$/i' => Attribute::INT,
            '/^height$/i' => Attribute::INT
        );

        return array_merge(
            $objectAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function isInteractiveContent() : bool
    {
        return true;
    }

    public function isTransparentElement()
    {
        return true;
    }
}
