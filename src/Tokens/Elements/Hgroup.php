<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\HeadingContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "hgroup" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-hgroup-element
 */
class Hgroup extends OpenElement implements FlowContent, HeadingContent
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // One or more "h1", "h2", "h3", "h4", "h5", "h6", and
        // "template" elements required.
        $headingContentElementCount = 0;
        foreach ($this->children as $child) {
            if ($child->getType() === Token::COMMENT) {
                continue;
            }

            if ($child->getType() !== Token::ELEMENT) {
                $logger->debug('Removing ' . $child . '. Only "h1"-"h6" and "template" elements allowed as child of "hgroup" element.');
                $this->removeChild($child);

                continue;
            }

            if ($child->getName() !== 'h1' &&
                $child->getName() !== 'h2' &&
                $child->getName() !== 'h3' &&
                $child->getName() !== 'h4' &&
                $child->getName() !== 'h5' &&
                $child->getName() !== 'h6' &&
                $child->getName() !== 'tempate') {
                $logger->debug('Removing ' .  $child . '. Only "h1"-"h6" and "template" elements allowed as child of "hgroup" element.');
                $this->removeChild($child);

                continue;
            }
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        foreach ($this->children as $child) {
            if ($child instanceof HeadingContent) {
                return false;
            }
        }

        // Handle no child HeadingContent.
        if ($headingContentElementCount == 0) {
            $logger->debug($this . ' must contain at least one of the following elements: "h1", "h2", "h3", "h4", "h5", "h6", or "template".');

            return true;
        }

        return false;
    }
}
