<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "dd" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-dd-element
 */
class Dd extends OpenElement
{
    protected function doClean(LoggerInterface $logger)
    {
        // Must be child of "dl" element.
        $parent = $this->getParent();
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT &&
            $parent !== null &&
            $parent->getName() != 'dl') {
            $logger->debug('Element "dt" must be a child of a "dl" element.');

            return false;
        }

        return true;
    }
}
