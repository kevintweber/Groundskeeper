<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "tr" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-tr-element
 */
class Tr extends OpenElement
{
    protected function doClean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            // "table" must be parent.
            $parent = $this->getParent();
            if ($parent !== null &&
                $parent->getName() != 'thead' &&
                $parent->getName() != 'tbody' &&
                $parent->getName() != 'tfoot' &&
                $parent->getName() != 'table') {
                $logger->debug('Element "tr" must be a child of the "thead", "tbody", "tfoot", or "table" elements.');

                return false;
            }

            // Children can be "td", "th", and script supporting elements.
            foreach ($this->children as $child) {
                if ($child->getType() == Token::COMMENT) {
                    continue;
                }

                if ($child->getType() !== Token::ELEMENT) {
                    $logger->debug('Removing ' . $child . '. Only elements allowed as children of "tr" element.');
                    $this->removeChild($child);

                    continue;
                }

                if ($child->getName() == 'td' ||
                    $child->getName() == 'th' ||
                    $child instanceof ScriptSupporting) {
                    continue;
                }

                $logger->debug('Removing ' . $child . '. Only "td", "th", and script supporting elements allowed as children of "tr" element.');
                $this->removeChild($child);
            }
        }

        return true;
    }
}
