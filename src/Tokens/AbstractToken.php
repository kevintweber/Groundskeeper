<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

abstract class AbstractToken implements Token
{
    /** @var int */
    private $depth;

    /** @var null|boolean */
    protected $isValid;

    /** @var null|Token */
    private $parent;

    /** @var string */
    private $type;

    /**
     * Constructor
     */
    public function __construct($type, $parent = null)
    {
        if (!$this->isValidType($type)) {
            throw new \InvalidArgumentException('Invalid type: ' . $type);
        }

        $this->isValid = null;
        $this->setParent($parent);
        $this->type = $type;
    }

    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Getter for 'isValid'.
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Chainable setter for 'isValid'.
     */
    public function setIsValid($isValid)
    {
        $this->isValid = (boolean) $isValid;

        return $this;
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
    public function setParent($parent = null)
    {
        if ($parent !== null &&
            !$parent instanceof \Groundskeeper\Tokens\Token &&
            !$parent instanceof \Kevintweber\HtmlTokenizer\Tokens\Token) {
            throw new \InvalidArgumentException('Invalid parent type.');
        }

        $this->depth = 0;
        if ($parent !== null) {
            $this->depth = $parent->getDepth() + 1;
        }

        $this->parent = $parent;

        return $this;
    }

    public function getType()
    {
        return $this->type;
    }

    public function validate(Configuration $configuration)
    {
        // If invalidated externally, then we don't allow it to be changed.
        if ($this->isValid === false) {
            return;
        }

        $this->isValid = $configuration->isAllowedType($this->type);
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
