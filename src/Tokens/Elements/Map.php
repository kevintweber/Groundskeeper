<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\TransparentElement;

/**
 * "map" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-map-element
 */
class Map extends OpenElement implements FlowContent, PhrasingContent, TransparentElement
{
    protected function getAllowedAttributes()
    {
        $mapAllowedAttributes = array(
            '/^name$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $mapAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function isTransparentElement() : bool
    {
        return true;
    }
}
