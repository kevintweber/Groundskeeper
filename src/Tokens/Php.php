<?php

namespace Groundskeeper\Tokens;

class Php extends AbstractValuedToken implements NonParticipating
{
    public function getType()
    {
        return Token::PHP;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        return $prefix . '<?php ' . $this->getValue() . ' ?>' . $suffix;
    }
}
