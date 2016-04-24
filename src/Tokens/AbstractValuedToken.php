<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

abstract class AbstractValuedToken extends AbstractToken
{
    /** @var string */
    private $value;

    /**
     * Constructor
     */
    public function __construct($type, Configuration $configuration, Token $parent = null, $value = null)
    {
        parent::__construct($type, $configuration, $parent);

        $this->value = $value;
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
