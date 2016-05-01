<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

class Title extends OpenElement implements MetadataContent
{
    protected function doClean(LoggerInterface $logger = null)
    {
        // TITLE must contain only non-whitespace text.
        foreach ($this->children as $child) {
            if ($child->getType() == Token::COMMENT) {
                continue;
            }

            if ($child->getType() != Token::TEXT &&
                $this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                $this->removeChild($child);
                if ($logger !== null) {
                    $logger->debug('Removing ' . $child . '. Only text allowed inside TITLE.');
                }
            }
        }

        return true;
    }
}
