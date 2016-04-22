<?php

namespace Groundskeeper\Tokens;

abstract class AbstractToken implements Token
{
    /** @var int */
    private $depth;

    /** @var null|Token */
    private $parent;

    /** @var string */
    private $type;

    /**
     * Constructor
     */
    public function __construct($type, Token $parent = null)
    {
        if (!$this->isValidType($type)) {
            throw new \InvalidArgumentException('Invalid type: ' . $type);
        }

        $this->setParent($parent);
        $this->type = $type;
    }

    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Getter for 'parent'.
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
