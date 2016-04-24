<?php

namespace Groundskeeper\Output;

use Groundskeeper\Tokens\Token;

class Pretty extends AbstractOutput
{
    protected function getHtmlFromToken(Token $token)
    {
        return $token->toHtml('', "\n");
    }
}
