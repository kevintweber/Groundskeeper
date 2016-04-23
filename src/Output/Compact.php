<?php

namespace Groundskeeper\Output;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Token;

class Compact extends AbstractOutput
{
    protected function printToken(Configuration $configuration, Token $token)
    {
        return $token->toString($configuration);
    }
}
