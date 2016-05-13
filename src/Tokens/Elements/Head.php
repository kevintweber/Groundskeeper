<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "head" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-head-element
 */
class Head extends OpenElement
{
    protected function fixSelf(LoggerInterface $logger)
    {
        // Look for "title" element
        foreach ($this->children as $child) {
            if ($child instanceof Title) {
                return;
            }
        }

        // Missing title.
        $logger->debug('Adding "title" element. One "title" element required.');
        $title = new Title($this->configuration, 'title');
        $this->prependChild($title);
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // "head" element must contain only metadata content elements.
        // "head" element must contain exactly one "title" element.
        // "head" element must contain either 0 or 1 "base" element.
        $titleCount = 0;
        $baseCount = 0;
        foreach ($this->children as $child) {
            if ($child->getType() == Token::COMMENT) {
                continue;
            }

            if (!$child instanceof MetadataContent) {
                $logger->debug('Removing ' . $child . '. Only children of metadata content allowed.');
                $this->removeChild($child);

                continue;
            }

            if ($child->getType() != Token::ELEMENT) {
                continue;
            }

            if ($child->getName() == 'title') {
                ++$titleCount;
                if ($titleCount > 1) {
                    $logger->debug('Removing ' . $child . '. Only one "title" element allowed.');
                    $this->removeChild($child);

                    continue;
                }
            } elseif ($child->getName() == 'base') {
                ++$baseCount;
                if ($baseCount > 1) {
                    $logger->debug('Removing ' . $child . '. Maximum one "base" element allowed.');

                    $this->removeChild($child);

                    continue;
                }
            }
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // "head" element must be a child of "html" element.
        if ($this->getParent() !== null &&
            $this->getParent()->getType() === Token::ELEMENT &&
            $this->getParent()->getName() != 'html') {
            $logger->debug($this . ' must be a child of "html" element.');

            return true;
        }

        return false;
    }
}
