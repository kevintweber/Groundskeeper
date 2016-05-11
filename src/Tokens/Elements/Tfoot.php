<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "tfoot" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-tfoot-element
 */
class Tfoot extends OpenElement
{
    protected function doClean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            // "table" must be parent.
            $parent = $this->getParent();
            if ($parent !== null && $parent->getName() != 'table') {
                $logger->debug('Element "tfoot" must be a child of the "table" element.');

                return false;
            }

            // Children can be "tr" and script supporting elements.
            foreach ($this->children as $child) {
                if ($child->getType() == Token::COMMENT) {
                    continue;
                }

                if ($child->getType() !== Token::ELEMENT) {
                    $logger->debug('Removing ' . $child . '. Only elements allowed as children of "tfoot" element.');
                    $this->removeChild($child);

                    continue;
                }

                if ($child->getName() == 'tr' ||
                    $child instanceof ScriptSupporting) {
                    continue;
                }

                $logger->debug('Removing ' . $child . '. Only "tr" and script supporting elements allowed as children of "tfoot" element.');
                $this->removeChild($child);
            }
        }

        return true;
    }
}
