<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\HeadingContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "hgroup" element
 */
class Hgroup extends OpenElement implements FlowContent, HeadingContent
{
    protected function doClean(LoggerInterface $logger = null)
    {
        // One or more "h1", "h2", "h3", "h4", "h5", "h6", and
        // "template" elements required.
        $headingContentElementCount = 0;
        foreach ($this->children as $child) {
            if ($child->getType() === Token::COMMENT) {
                continue;
            }

            if ($child->getType() !== Token::ELEMENT) {
                if ($logger !== null) {
                    $logger->debug('Removing ' . $child . '. Only "h1"-"h6" and "template" elements allowed as child of "hgroup" element.');
                }

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
                if ($logger !== null) {
                    $logger->debug('Removing ' . $child . '. Only "h1"-"h6" and "template" elements allowed as child of "hgroup" element.');
                }

                $this->removeChild($child);
                continue;
            }
        }

        return true;
    }
}
