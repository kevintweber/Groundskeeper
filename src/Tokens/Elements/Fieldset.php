<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningRoot;

/**
 * "fieldset" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-fieldset-element
 */
class Fieldset extends OpenElement implements FlowContent, SectioningRoot
{
    protected function getAllowedAttributes()
    {
        $fieldsetAllowedAttributes = array(
            '/^disabled$/i' => Attribute::BOOL,
            '/^form$/i' => Attribute::CS_STRING,
            '/^name$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $fieldsetAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function fixSelf(LoggerInterface $logger)
    {
        // If "legend" element is present, then it must be the first
        // child of "fieldset" element.
        /// @todo
    }
}
