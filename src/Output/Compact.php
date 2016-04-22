<?php

namespace Groundskeeper\Output;

use Groundskeeper\Tokens\Token;

class Compact extends AbstractOutput
{
    protected function printToken(Token $token)
    {
        return $token->toString();
    }
}
