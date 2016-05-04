<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Psr\Log\LoggerInterface;

class DocType extends AbstractValuedToken implements Cleanable
{
    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, $value = null)
    {
        parent::__construct(Token::DOCTYPE, $configuration, $value);
    }

    /**
     * Required by the Cleanable interface.
     */
    public function clean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        // DocType must not have any parent elements.
        $result = $this->getParent() === null;
        if (!$result) {
            $logger->debug('Removing ' . $this . '.  DocType cannot be a child of any element.');
        }

        return $result;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        return $prefix . '<!DOCTYPE ' . $this->getValue() . '>' . $suffix;
    }
}
