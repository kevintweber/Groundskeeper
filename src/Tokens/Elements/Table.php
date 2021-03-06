<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\NonParticipating;
use Psr\Log\LoggerInterface;

/**
 * "table" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-table-element
 */
class Table extends OpenElement implements FlowContent
{
    /**
     * @todo Deal with validation of the order of child elements.
     */
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating ||
                $child instanceof Caption ||
                $child instanceof Colgroup ||
                $child instanceof Thead ||
                $child instanceof Tbody ||
                $child instanceof Tr ||
                $child instanceof Tfoot ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only "caption", "colgroup", "thead", "tbody", "tr", "tfoot", and script supporting elements allowed as children of "table" element.');
            $this->removeChild($child);
        }
    }
}
