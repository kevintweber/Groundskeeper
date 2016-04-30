<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

class Title extends OpenElement implements MetadataContent
{
    /**
     * Required by the Cleanable interface.
     */
    public function clean(LoggerInterface $logger = null)
    {
        if ($this->configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        parent::clean($logger);

        // TITLE must contain only non-whitespace text.
        foreach ($this->children as $key => $child) {
            if ($child->getType() != Token::TEXT) {
                if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                    return false;
                }

                $this->removeChildTokenHelper(
                    $key,
                    'Removing invalid token (' . $child->getType() . '). TITLE may contain only text.',
                    $logger
                );
            }
        }
    }
}
