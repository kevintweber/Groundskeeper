<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Psr\Log\LoggerInterface;

class DocType extends AbstractValuedToken implements Cleanable
{
    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, $parent = null, $value = null)
    {
        parent::__construct(Token::DOCTYPE, $configuration, $parent, $value);
    }

    /**
     * Required by the Cleanable interface.
     */
    public function clean(LoggerInterface $logger = null)
    {
        if ($this->configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        // DocType must not have any parent elements.
        return $this->getParent() === null;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        return $prefix . '<!DOCTYPE ' . $this->getValue() . '>' . $suffix;
    }
}
