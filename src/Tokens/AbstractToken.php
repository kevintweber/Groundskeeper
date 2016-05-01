<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Exceptions\ValidationException;
use Psr\Log\LoggerInterface;

/**
 * A base class for all tokens.
 */
abstract class AbstractToken implements Token
{
    /** @var Configuration */
    protected $configuration;

    /** @var int */
    private $depth;

    /** @var null|Token */
    private $parent;

    /** @var string */
    private $type;

    /**
     * Constructor
     */
    public function __construct($type, Configuration $configuration)
    {
        if ($type !== Token::CDATA
            && $type !== Token::COMMENT
            && $type !== Token::DOCTYPE
            && $type !== Token::ELEMENT
            && $type !== Token::TEXT) {
            throw new \InvalidArgumentException('Invalid type: ' . $type);
        }

        $this->configuration = $configuration;
        $this->depth = 0;
        $this->parent = null;
        $this->type = $type;
    }

    /**
     * Required by the Token interface.
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Required by the Token interface.
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Chainable setter for 'parent'.
     */
    public function setParent(Token $parent = null)
    {
        $this->depth = 0;
        if ($parent instanceof Token) {
            $this->depth = $parent->getDepth() + 1;
        }

        $this->parent = $parent;

        return $this;
    }

    /**
     * Required by the Token interface.
     */
    public function getType()
    {
        return $this->type;
    }

    public static function cleanChildTokens(Configuration $configuration, array &$children, LoggerInterface $logger = null)
    {
        if ($configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        foreach ($children as $key => $child) {
            if ($child instanceof Cleanable) {
                $isClean = $child->clean($logger);
                if (!$isClean  && $configuration->get('clean-strategy') !== Configuration::CLEAN_STRATEGY_LENIENT) {
                    if ($logger !== null) {
                        $logger->debug('Unable to fix.  Removing ' . $child);
                    }

                    unset($children[$key]);
                }
            }
        }

        return true;
    }

    public function __toString()
    {
        return ucfirst($this->getType());
    }
}
