<?php

namespace Groundskeeper\Output;

use Groundskeeper\Tokens\Token;

abstract class AbstractOutput
{
    /**
     * Will print out HTML from tokens.
     *
     * @param array $tokens
     *
     * @return string
     */
    public function printTokens(array $tokens)
    {
        $output = '';
        foreach ($tokens as $token) {
            $output .= $this->printToken($token);
        }

        return $output;
    }

    abstract protected function printToken(Token $token);
}
