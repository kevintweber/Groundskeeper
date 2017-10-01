<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
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

    /** @var int */
    private $line;

    /** @var null|Token */
    private $parent;

    /** @var int */
    private $position;

    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, int $line, int $position)
    {
        $this->configuration = $configuration;
        $this->depth = 0;
        $this->line = $line;
        $this->parent = null;
        $this->position = $position;
    }

    /**
     * Required by the Token interface.
     */
    public function getDepth() : int
    {
        return $this->depth;
    }

    /**
     * Getter for 'line'.
     */
    public function getLine() : int
    {
        return $this->line;
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
    }

    public function hasAncestor(Element $element) : bool
    {
        if ($this->parent === null) {
            return false;
        }

        if ($this->parent->getType() === Token::ELEMENT &&
            $this->parent->getName() === $element->getName()) {
            return true;
        }

        return $this->parent->hasAncestor($element);
    }

    /**
     * Getter for 'position'.
     */
    public function getPosition() : int
    {
        return $this->position;
    }

    public static function cleanChildTokens(Configuration $configuration, array &$children, LoggerInterface $logger)
    {
        if ($configuration->get('clean-strategy') === Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        foreach ($children as $key => $child) {
            if ($child instanceof Cleanable) {
                $isClean = $child->clean($logger);
                if (!$isClean && $configuration->get('clean-strategy') !== Configuration::CLEAN_STRATEGY_LENIENT) {
                    unset($children[$key]);
                }
            }
        }

        return true;
    }

    abstract public function getType() : string;

    public function __toString()
    {
        return ucfirst($this->getType()) . ' (line: ' . $this->line .
            '; position: ' . $this->position . ')';
    }
}
