<?php

namespace Groundskeeper;

use Groundskeeper\Tokens\Tokenizer;

class Groundskeeper
{
    /** @var Configuration */
    private $configuration;

    /**
     * Constructor
     *
     * @param array|Configuration $options
     */
    public function __construct($options = array())
    {
        if ($options instanceof Configuration) {
            $this->configuration = $options;

            return;
        }

        if (!is_array($options)) {
            throw new \InvalidArgumentException('Invalid option type.');
        }

        $this->configuration = new Configuration($options);
    }

    public function clean($html)
    {
        // Tokenize
        $tokenizer = new Tokenizer($this->configuration);
        $tokens = $tokenizer->tokenize($html);

        // Clean
        foreach ($tokens as $token) {
            $token->validate($this->configuration);
        }

        // Output
        $outputClassName = 'Groundskeeper\\Output\\' .
            ucfirst($this->configuration->get('output'));
        $output = new $outputClassName($this->configuration);

        return $output->printTokens($tokens);
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }
}
