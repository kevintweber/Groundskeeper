<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\NonParticipating;
use Psr\Log\LoggerInterface;

/**
 * "tr" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-tr-element
 */
class Tr extends OpenElement
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // Children can be "td", "th", and script supporting elements.
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating ||
                $child instanceof Td ||
                $child instanceof Th ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only "td", "th", and script supporting elements allowed as children of "tr" element.');
            $this->removeChild($child);
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger) : bool
    {
        // "table" must be parent.
        $parent = $this->getParent();
        if ($parent !== null &&
            !$parent instanceof Thead &&
            !$parent instanceof Tbody &&
            !$parent instanceof Tfoot &&
            !$parent instanceof Table) {
            $logger->debug('Removing ' . $this . '. Must be a child of the "thead", "tbody", "tfoot", or "table" elements.');

            return true;
        }

        return false;
    }
}
