<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
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
            '/^alt$/i' => Attribute::CS_STRING,
            '/^coords$/i' => Attribute::CS_STRING,
            '/^shape$/i' => Attribute::CS_STRING,
            '/^href$/i' => Attribute::URI,
            '/^target$/i' => Attribute::CS_STRING,
            '/^download$/i' => Attribute::CS_STRING,
            '/^ping$/i' => Attribute::URI,
            '/^rel$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $areaAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
