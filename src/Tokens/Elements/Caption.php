<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "caption" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-caption-element
 */
class Caption extends OpenElement implements FlowContent
{
    /**
     * @todo Caption must be *first* child of table.
     */
    protected function doClean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            $parent = $this->getParent();
            if ($parent->getType() !== Token::ELEMENT &&
                $parent->getName() != 'table') {
                $logger->debug('Element "caption" must be child of "table" element.');

                return false;
            }
        }

        return true;
    }
}
