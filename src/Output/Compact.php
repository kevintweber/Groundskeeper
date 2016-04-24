<?php

namespace Groundskeeper\Output;

use Groundskeeper\Tokens\Token;

class Compact extends AbstractOutput
{
    protected function getHtmlFromToken(Token $token)
    {
        return $token->toHtml('', '');
    }
}
