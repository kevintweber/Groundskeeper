<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\NonParticipating;
use Psr\Log\LoggerInterface;

/**
 * "tbody" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-tbody-element
 */
class Tbody extends OpenElement
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // Children can be "tr" and script supporting elements.
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating ||
                $child instanceof Tr ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only "tr" and script supporting elements allowed as children of "tbody" element.');
            $this->removeChild($child);
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger) : bool
    {
        // "table" must be parent.
        $parent = $this->getParent();
        if ($parent !== null && !$parent instanceof Table) {
            $logger->debug('Removing ' . $this . '. Must be a child of the "table" element.');

            return true;
        }

        return false;
    }
}
