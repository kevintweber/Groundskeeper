<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\NonParticipating;
use Psr\Log\LoggerInterface;

/**
 * "ul" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-ul-element
 */
class Ul extends OpenElement implements FlowContent
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // Only "li" and ScriptSupporting elements allowed.
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating ||
                $child instanceof Li ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only elements "li" and script supporting elements allowed as children of "ul" element.');
            $this->removeChild($child);
        }
    }
}
