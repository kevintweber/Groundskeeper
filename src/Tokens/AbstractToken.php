<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

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
    public function __construct($type, Configuration $configuration, Token $parent = null)
    {
        if (!$this->isValidType($type)) {
            throw new \InvalidArgumentException('Invalid type: ' . $type);
        }

        $this->configuration = $configuration;
        $this->setParent($parent);
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

    protected function isValidType($type)
    {
        return $type === Token::CDATA
            || $type === Token::COMMENT
            || $type === Token::DOCTYPE
            || $type === Token::ELEMENT
            || $type === Token::TEXT;
    }
}
