<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

class Head extends OpenElement
{
    protected function doClean(LoggerInterface $logger = null)
    {
        // HEAD must contain only metadata content elements.
        // HEAD must contain exactly one TITLE element.
        // HEAD must contain either 0 or 1 BASE element.
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
                    $this->removeChild($child);
                    if ($logger !== null) {
                        $logger->debug('Removing ' . $child . '. Only one TITLE element allowed.');
                    }
                }
            } elseif ($child->getName() == 'base') {
                $baseCount++;
                if ($baseCount > 1 &&
                    $this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                    $this->removeChild($child);
                    if ($logger !== null) {
                        $logger->debug('Removing ' . $child . '. Maximum one BASE element allowed.');
                    }
                }
            }
        }

        // Missing title.
        if ($titleCount == 0) {
            $title = new Title($this->configuration, 'title');
            $this->prependChild($title);
        }

        return true;
    }
}
