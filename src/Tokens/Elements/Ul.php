<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "ul" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-ul-element
 */
class Ul extends OpenElement
{
    protected function doClean(LoggerInterface $logger)
    {
        // Only "li" and ScriptSupporting elements allowed.
        foreach ($this->children as $child) {
            if ($child->getType() == Token::COMMENT) {
                continue;
            }

            if ($child->getType() != Token::ELEMENT) {
                if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                    $logger->debug('Removing ' . $child . '. Only elements "li" and script supporting elements allowed as children of "ul" element.');
                    $this->removeChild($child);
                }

                continue;
            }

            if ($child->getName() == 'li' || $child instanceof ScriptSupporting) {
                continue;
            }

            if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                $logger->debug('Removing ' . $child . '. Only elements "li" and script supporting elements allowed as children of "ul" element.');
                $this->removeChild($child);
            }
        }

        return true;
    }
}
