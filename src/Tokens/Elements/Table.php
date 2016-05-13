<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\Token;
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
            if ($child->getType() == Token::COMMENT) {
                continue;
            }

            if ($child->getType() !== Token::ELEMENT) {
                $logger->debug('Removing ' . $child . '. Only elements allowed as children of "table" element.');
                $this->removeChild($child);

                continue;
            }

            if ($child->getName() == 'caption' ||
                $child->getName() == 'colgroup' ||
                $child->getName() == 'thead' ||
                $child->getName() == 'tbody' ||
                $child->getName() == 'tr' ||
                $child->getName() == 'tfoot' ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only "caption", "colgroup", "thead", "tbody", "tr", "tfoot", and script supporting elements allowed as children of "table" element.');
            $this->removeChild($child);
        }
    }
}
