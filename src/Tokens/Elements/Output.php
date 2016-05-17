<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "output" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-output-element
 */
class Output extends OpenElement implements FlowContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $outputAllowedAttributes = array(
            '/^for$/i' => Attribute::CS_STRING,
            '/^form$/i' => Attribute::CS_STRING,
            '/^name$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $outputAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
