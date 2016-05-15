<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\NonParticipating;
use Psr\Log\LoggerInterface;

/**
 * "ol" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-ol-element
 */
class Ol extends OpenElement implements FlowContent
{
    protected function getAllowedAttributes()
    {
        $olAllowedAttributes = array(
            '/^reversed$/i' => Attribute::BOOL,
            '/^start$/i' => Attribute::INT,
            '/^type$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $olAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // Only "li" and ScriptSupporting elements allowed.
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating ||
                $child instanceof Li ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only elements "li" and script supporting elements allowed as children of "ol" element.');
            $this->removeChild($child);
        }
    }
}
