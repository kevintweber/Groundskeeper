<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

class DocType extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, $parent = null, $value = null)
    {
        parent::__construct(Token::DOCTYPE, $configuration, $parent, $value);
    }

    /**
     * @todo DocType must be preceeded by either nothing or a comment
     */
    protected function isValid()
    {
        // DocType must not have any parent elements.
        return $this->getParent() === null;
    }

    protected function buildHtml($prefix, $suffix)
    {
        return $prefix . '<!DOCTYPE ' . $this->getValue() . '>' . $suffix;
    }
}
