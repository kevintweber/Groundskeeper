<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "area" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-area-element
 */
class Area extends ClosedElement implements FlowContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $areaAllowedAttributes = array(
            '/^alt$/i' => Element::ATTR_CS_STRING,
            '/^coords$/i' => Element::ATTR_CS_STRING,
            '/^shape$/i' => Element::ATTR_CS_STRING,
            '/^href$/i' => Element::ATTR_URI,
            '/^target$/i' => Element::ATTR_CS_STRING,
            '/^download$/i' => Element::ATTR_CS_STRING,
            '/^ping$/i' => Element::ATTR_URI,
            '/^rel$/i' => Element::ATTR_CS_STRING,
        );

        return array_merge(
            $areaAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
