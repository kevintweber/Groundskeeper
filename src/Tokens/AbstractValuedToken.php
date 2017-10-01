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
    public function __construct(Configuration $configuration, int $line, int $position, string $value = null)
    {
        parent::__construct($configuration, $line, $position);

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
    public function setValue(string $value = null)
    {
        $this->value = $value;
    }
}
