<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InlineElement;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Psr\Log\LoggerInterface;

/**
 * "dfn" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-dfn-element
 */
class Dfn extends OpenElement implements FlowContent, PhrasingContent, InlineElement
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // There must be no dfn element descendants.
        foreach ($this->children as $child) {
            if ($child instanceof self) {
                $logger->debug('Removing ' . $child . '. Element "dfn" cannot contain "dfn" elements.');
                $this->removeChild($child);
            }
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        $dfn = new self($this->configuration);
        if ($this->hasAncestor($dfn)) {
            $logger->debug('Removing ' . $child . '. Element "dfn" cannot contain "dfn" elements.');

            return true;
        }

        return false;
    }
}
