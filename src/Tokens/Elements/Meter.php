<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "meter" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-meter-element
 */
class Meter extends OpenElement implements FlowContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $meterAllowedAttributes = array(
            '/^value$/i' => Attribute::CS_STRING,
            '/^min$/i' => Attribute::CS_STRING,
            '/^max$/i' => Attribute::CS_STRING,
            '/^low$/i' => Attribute::CS_STRING,
            '/^high$/i' => Attribute::CS_STRING,
            '/^optimum$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $meterAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        $meter = new self($this->configuration, 'meter');
        if ($this->hasAncestor($meter)) {
            $logger->debug('Removing ' . $this . '. "Meter" element cannot contain other "meter" elements.');

            return true;
        }

        return false;
    }
}
