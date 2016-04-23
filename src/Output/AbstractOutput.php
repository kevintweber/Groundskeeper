<?php

namespace Groundskeeper\Output;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Token;

abstract class AbstractOutput
{
    /** @var Configuration */
    private $configuration;

    /**
     * Constructor
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

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
            $output .= $this->printToken($this->configuration, $token);
        }

        return $output;
    }

    abstract protected function printToken(Configuration $configuration, Token $token);
}
