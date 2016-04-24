<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

class DocType extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct($parent = null, $value = null)
    {
        parent::__construct(Token::DOCTYPE, $parent, $value);
    }

    public function validate(Configuration $configuration)
    {
        parent::validate($configuration);

        if ($this->isValid === true) {
            $this->isValid = $this->getParent() === null;
        }
    }

    public function toString(Configuration $configuration, $prefix = '', $suffix = '')
    {
        if (!$this->isValid) {
            return '';
        }

        return $prefix . '<!DOCTYPE ' . $this->getValue() . '>' . $suffix;
    }
}
