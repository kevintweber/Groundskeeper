<?php

namespace Groundskeeper\Tokens;

class Php extends AbstractValuedToken implements NonParticipating
{
    public function getType() : string
    {
        return Token::PHP;
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml(string $prefix, string $suffix) : string
    {
        return $prefix . '<?php ' . $this->getValue() . ' ?>' . $suffix;
    }
}
