<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

class Php extends AbstractValuedToken
{
    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, $value = null)
    {
        parent::__construct(Token::PHP, $configuration, $value);
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        return $prefix . '<?php ' . $this->getValue() . ' ?>' . $suffix;
    }
}
