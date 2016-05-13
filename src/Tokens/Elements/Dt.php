<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\HeadingContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningContent;
use Groundskeeper\Tokens\Token;
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
            if ($child->getType() == Token::COMMENT ||
                $child->getType() == Token::TEXT) {
                continue;
            }

            if ($child->getType() != Token::ELEMENT) {
                $logger->debug('Removing ' . $child . '. Element "dt" cannot contain "header", "footer", section content, or heading content elements.');
                $this->removeChild($child);

                continue;
            }

            if ($child->getName() == 'header' ||
                $child->getName() == 'footer' ||
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
            $parent->getName() != 'dl') {
            $logger->debug($this . ' must be a child of a "dl" element.');

            return true;
        }

        return false;
    }
}
