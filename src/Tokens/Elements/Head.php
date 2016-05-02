<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "head" element
 */
class Head extends OpenElement
{
    protected function doClean(LoggerInterface $logger = null)
    {
        // "head" element must be a child of "html" element.
        if ($this->getParent() !== null &&
            $this->getParent()->getType() === Token::ELEMENT &&
            $this->getParent()->getName() != 'html') {
            if ($logger !== null) {
                $logger->debug('Element "head" must be a child of "html" element.');
            }

            return false;
        }

        // "head" element must contain only metadata content elements.
        // "head" element must contain exactly one "title" element.
        // "head" element must contain either 0 or 1 "base" element.
        $titleCount = 0;
        $baseCount = 0;
        foreach ($this->children as $child) {
            if (!$child instanceof MetadataContent &&
                $child->getType() !== 'comment' &&
                $this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                $this->removeChild($child);
                if ($logger !== null) {
                    $logger->debug('Removing ' . $child . '. Only children of metadata content allowed.');
                }
            }

            if ($child->getType() != Token::ELEMENT) {
                continue;
            }

            if ($child->getName() == 'title') {
                $titleCount++;
                if ($titleCount > 1 &&
                    $this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                    if ($logger !== null) {
                        $logger->debug('Removing ' . $child . '. Only one "title" element allowed.');
                    }

                    $this->removeChild($child);
                }
            } elseif ($child->getName() == 'base') {
                $baseCount++;
                if ($baseCount > 1 &&
                    $this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                    if ($logger !== null) {
                        $logger->debug('Removing ' . $child . '. Maximum one "base" element allowed.');
                    }

                    $this->removeChild($child);
                }
            }
        }

        // Missing title.
        if ($titleCount == 0) {
            if ($logger !== null) {
                $logger->debug('Adding "title" element. One "title" element required.');
            }

            $title = new Title($this->configuration, 'title');
            $this->prependChild($title);
        }

        return true;
    }
}
