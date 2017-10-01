<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Psr\Log\LoggerInterface;

class DocType extends AbstractValuedToken implements Cleanable
{
    public function getType() : string
    {
        return Token::DOCTYPE;
    }

    /**
     * Required by the Cleanable interface.
     */
    public function clean(LoggerInterface $logger) : bool
    {
        $cleanStrategyConfiguration = $this->configuration->get('clean-strategy');
        if ($cleanStrategyConfiguration === Configuration::CLEAN_STRATEGY_NONE ||
            $cleanStrategyConfiguration === Configuration::CLEAN_STRATEGY_LENIENT) {
            return true;
        }

        // DocType must not have any parent elements.
        $result = $this->getParent() === null;
        if (!$result) {
            $logger->debug('Removing ' . $this . '. DocType cannot be a child of any element.');
        }

        return $result;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml(string $prefix, string $suffix) : string
    {
        return $prefix . '<!DOCTYPE ' . $this->getValue() . '>' . $suffix;
    }
}
