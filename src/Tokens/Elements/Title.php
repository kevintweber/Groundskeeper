<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "title" element
 */
class Title extends OpenElement implements MetadataContent
{
    protected function doClean(LoggerInterface $logger)
    {
        // TITLE must contain only non-whitespace text or comments.
        foreach ($this->children as $child) {
            if ($child->getType() == Token::COMMENT) {
                continue;
            }

            if ($child->getType() != Token::TEXT &&
                $this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                $logger->debug('Removing ' . $child . '. Only text allowed inside TITLE.');
                $this->removeChild($child);
            }
        }

        return true;
    }
}
