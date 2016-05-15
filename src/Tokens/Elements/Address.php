<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\HeadingContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningContent;
use Psr\Log\LoggerInterface;

/**
 * "address" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-address-element
 */
class Address extends OpenElement implements FlowContent
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // No HeadingContent descendants
        // No SectioningContent descendants
        // No "header", "footer", or "address" element descendants.
        foreach ($this->children as $child) {
            if ($child instanceof HeadingContent) {
                $logger->debug('Removing ' . $child . '. Heading Content elements not allowed as "address" element child.');
                $this->removeChild($child);

                continue;
            }

            if ($child instanceof SectioningContent) {
                $logger->debug('Removing ' . $child . '. Sectioning Content elements not allowed as "address" element child.');
                $this->removeChild($child);

                continue;
            }

            if ($child instanceof Header ||
                $child instanceof Footer ||
                $child instanceof self) {
                $logger->debug('Removing ' . $child . '. Elements "header", "footer", and "address" not allowed as "address" element child.');
                $this->removeChild($child);
            }
        }
    }
}
