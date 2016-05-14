<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\NonParticipating;
use Groundskeeper\Tokens\Text;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "title" element
 */
class Title extends OpenElement implements MetadataContent
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // TITLE must contain only non-whitespace text or comments.
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating) {
                continue;
            }

            if (!$child instanceof Text) {
                $logger->debug('Removing ' . $child . '. Only text allowed inside TITLE.');
                $this->removeChild($child);
            }
        }
    }
}
