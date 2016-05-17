<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningRoot;

/**
 * "dialog" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-dialog-element
 *
 * @todo Implement checks.
 */
class Dialog extends OpenElement implements FlowContent, SectioningRoot
{
    protected function getAllowedAttributes()
    {
        $dialogAllowedAttributes = array(
            '/^open$/i' => Attribute::BOOL
        );

        return array_merge(
            $dialogAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
