<?php

namespace Groundskeeper\Output;

use Groundskeeper\Tokens\Token;
use Groundskeeper\Tokens\TokenContainer;

abstract class AbstractOutput
{
    /**
     * Will output HTML from tokens.
     *
     * @param TokenContainer $tokenContainer
     *
     * @return string
     */
    public function __invoke(TokenContainer $tokenContainer)
    {
        $output = '';
        foreach ($tokenContainer->getChildren() as $token) {
            $output .= $this->getHtmlFromToken($token);
        }

        return trim($output);
    }

    /**
     * Will output an individual token to HTML.
     *
     * @param Token $token
     *
     * @return string
     */
    abstract protected function getHtmlFromToken(Token $token);
}
