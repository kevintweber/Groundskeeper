<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\TransparentElement;

/**
 * "slot" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-slot-element
 *
 * @todo Implement checks.
 */
class Slot extends OpenElement implements FlowContent, PhrasingContent, TransparentElement
{
    protected function getAllowedAttributes()
    {
        $slotAllowedAttributes = array(
            '/^name$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $slotAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function isTransparentElement() : bool
    {
        return true;
    }
}
