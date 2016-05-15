<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\NonParticipating;
use Groundskeeper\Tokens\Text;
use Psr\Log\LoggerInterface;

/**
 * "time" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-time-element
 */
class Time extends OpenElement implements FlowContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $timeAllowedAttributes = array(
            '/^datetime$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $timeAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // If attribute "datetime" is not present, then only TEXT type
        // children allowed.
        if (!$this->hasAttribute('datetime')) {
            foreach ($this->children as $child) {
                if ($child instanceof NonParticipating) {
                    continue;
                }

                if (!$child instanceof Text) {
                    $logger->debug('Removing ' . $child . '. Element "time" without "datetime" attribute may only contain TEXT.');
                    $this->removeChild($child);

                    continue;
                }
            }
        }
    }
}
