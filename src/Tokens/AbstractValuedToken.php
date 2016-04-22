<?php

namespace Groundskeeper\Tokens;

abstract class AbstractValuedToken extends AbstractToken
{
    /** @var string */
    private $value;

    /**
     * Constructor
     */
    public function __construct($type, Token $parent = null, $value = null)
    {
        parent::__construct($type, $parent);

        $this->setValue($value);
    }

    /**
     * Getter for 'value'.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Chainable setter for 'value'.
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
