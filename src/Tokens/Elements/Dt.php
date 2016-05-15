<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\HeadingContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningContent;
use Psr\Log\LoggerInterface;

/**
 * "dt" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-dt-element
 */
class Dt extends OpenElement
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // No "header", "footer", sectioning content, or heading content descendants.
        foreach ($this->children as $child) {
            if ($child instanceof Header ||
                $child instanceof Footer ||
                $child instanceof SectioningContent ||
                $child instanceof HeadingContent) {
                $logger->debug('Removing ' . $child . '. No "header", "footer", and sectioning content, or heading content elements allowed as children of "dt" element.');
                $this->removeChild($child);
            }
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // Must be child of "dl" element.
        $parent = $this->getParent();
        if ($parent !== null &&
            !$parent instanceof Dl) {
            $logger->debug('Removing ' . $this . '. Must be a child of a "dl" element.');

            return true;
        }

        return false;
    }
}
