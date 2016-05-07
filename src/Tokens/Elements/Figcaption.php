<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningRoot;
use Psr\Log\LoggerInterface;

/**
 * "figcaption" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-figcaption-element
 */
class Figcaption extends OpenElement implements FlowContent, SectioningRoot
{
    protected function doClean(LoggerInterface $logger)
    {
        // Must be child of "figure" element.
        $parent = $this->getParent();
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT &&
            $parent !== null &&
            $parent->getName() != 'figure') {
            $logger->debug('Element "figcaption" must be a child of a "figure" element.');

            return false;
        }

        return true;
    }
}
