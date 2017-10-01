<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\NonParticipating;
use Psr\Log\LoggerInterface;

/**
 * "tfoot" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-tfoot-element
 */
class Tfoot extends OpenElement
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

            $logger->debug('Removing ' . $child . '. Only "tr" and script supporting elements allowed as children of "tfoot" element.');
            $this->removeChild($child);
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger) : bool
    {
        // "table" must be parent.
        $parent = $this->getParent();
        if ($parent !== null && $parent->getName() !== 'table') {
            $logger->debug($this . ' must be a child of the "table" element.');

            return true;
        }

        return false;
    }
}
