<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\HeadingContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningContent;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "address" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-address-element
 */
class Address extends OpenElement implements FlowContent
{
    protected function doClean(LoggerInterface $logger)
    {
        // No HeadingContent descendants
        // No SectioningContent descendants
        // No "header", "footer", or "address" element descendants.
        foreach ($this->children as $child) {
            if ($child instanceof HeadingContent) {
                $logger->debug('Heading Content elements not allowed as "address" element child.');

                return false;
            }

            if ($child instanceof SectioningContent) {
                $logger->debug('Sectioning Content elements not allowed as "address" element child.');

                return false;
            }

            if ($child->getType() == Token::ELEMENT) {
                if ($child->getName() == 'header' ||
                    $child->getName() == 'footer' ||
                    $child->getName() == 'address') {
                    $logger->debug('Elements "header", "footer", and "address" not allowed as "address" element child.');

                    return false;
                }
            }
        }

        return true;
    }
}
