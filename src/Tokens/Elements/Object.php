<?php

namespace Groundskeeper\Tokens\Elements;

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
            '/^data$/i' => Element::ATTR_CS_STRING,
            '/^type$/i' => Element::ATTR_CS_STRING,
            '/^typemustmatch$/i' => Element::ATTR_CS_STRING,
            '/^name$/i' => Element::ATTR_CS_STRING,
            '/^usemap$/i' => Element::ATTR_CS_STRING,
            '/^form$/i' => Element::ATTR_CS_STRING,
            '/^width$/i' => Element::ATTR_INT,
            '/^height$/i' => Element::ATTR_INT
        );

        return array_merge(
            $objectAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function isInteractiveContent()
    {
        return true;
    }

    public function isTransparentElement()
    {
        return true;
    }
}
