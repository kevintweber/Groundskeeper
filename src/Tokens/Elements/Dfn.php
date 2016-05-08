<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InlineElement;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "dfn" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-dfn-element
 */
class Dfn extends OpenElement implements FlowContent, PhrasingContent, InlineElement
{
    protected function doClean(LoggerInterface $logger)
    {
        // There must be no dfn element descendants.
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            foreach ($this->children as $child) {
                if ($child->getType() == Token::COMMENT) {
                    continue;
                }

                if ($child->getType() == Token::ELEMENT &&
                    $child->getName() == 'dfn') {
                    $logger->debug('Removing ' . $child . '. Element "dfn" cannot contain "dfn" elements.');
                    $this->removeChild($child);
                }
            }
        }

        return true;
    }
}