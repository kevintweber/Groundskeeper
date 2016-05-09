<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "rp" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-rp-element
 */
class Rp extends OpenElement
{
    protected function doClean(LoggerInterface $logger)
    {
        // Must be child of "ruby" element.
        $parent = $this->getParent();
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT &&
            $parent !== null &&
            $parent->getName() != 'ruby') {
            $logger->debug('Element "rp" must be a child of a "ruby" element.');

            return false;
        }

        return true;
    }
}
