<?php

namespace Groundskeeper\Tokens\Elements;

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
    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // Must be child of "figure" element.
        $parent = $this->getParent();
        if ($parent !== null && !$parent instanceof Figure) {
            $logger->debug('Removing ' . $this . '. Must be a child of a "figure" element.');

            return true;
        }

        return false;
    }
}
