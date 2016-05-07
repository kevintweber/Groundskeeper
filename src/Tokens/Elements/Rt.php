<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\HeadingContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningContent;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "rt" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-rt-element
 */
class Rt extends OpenElement
{
    protected function doClean(LoggerInterface $logger)
    {
        // Must be child of "ruby" element.
        $parent = $this->getParent();
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT &&
            $parent !== null &&
            $parent->getName() != 'ruby') {
            $logger->debug('Element "rt" must be a child of a "ruby" element.');

            return false;
        }

        return true;
    }
}
