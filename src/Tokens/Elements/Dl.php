<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "dl" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-dl-element
 */
class Dl extends OpenElement implements FlowContent
{
    protected function doClean(LoggerInterface $logger)
    {
        // Only "dd", "dt", and ScriptSupporting elements allowed.
        foreach ($this->children as $child) {
            if ($child->getType() == Token::COMMENT) {
                continue;
            }

            if ($child->getType() != Token::ELEMENT) {
                if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                    $logger->debug('Removing ' . $child . '. Only elements "dd", "dt", and script supporting elements allowed as children of "dl" element.');
                    $this->removeChild($child);
                }

                continue;
            }

            if ($child->getName() == 'dd' ||
                $child->getName() == 'dt' ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                $logger->debug('Removing ' . $child . '. Only elements "dd", "dt", and script supporting elements allowed as children of "dl" element.');
                $this->removeChild($child);
            }
        }

        return true;
    }
}