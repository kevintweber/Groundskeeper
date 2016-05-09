<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "ul" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-ul-element
 */
class Ul extends OpenElement implements FlowContent
{
    protected function doClean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            // Only "li" and ScriptSupporting elements allowed.
            foreach ($this->children as $child) {
                if ($child->getType() == Token::COMMENT) {
                    continue;
                }

                if ($child->getType() != Token::ELEMENT) {
                    $logger->debug('Removing ' . $child . '. Only elements "li" and script supporting elements allowed as children of "ul" element.');
                    $this->removeChild($child);

                    continue;
                }

                if ($child->getName() == 'li' || $child instanceof ScriptSupporting) {
                    continue;
                }

                $logger->debug('Removing ' . $child . '. Only elements "li" and script supporting elements allowed as children of "ul" element.');
                $this->removeChild($child);
            }
        }

        return true;
    }
}
