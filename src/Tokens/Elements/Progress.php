<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "progress" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-progress-element
 */
class Progress extends OpenElement implements FlowContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $progressAllowedAttributes = array(
            '/^value$/i' => Attribute::CS_STRING,
            '/^max$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $progressAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
