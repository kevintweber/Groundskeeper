<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningRoot;

/**
 * "details" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-details-element
 */
class Details extends OpenElement implements FlowContent, SectioningRoot, InteractiveContent
{
    protected function getAllowedAttributes()
    {
        $detailsAllowedAttributes = array(
            '/^open$/i' => Attribute::BOOL
        );

        return array_merge(
            $detailsAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function isInteractiveContent()
    {
        return true;
    }
}
