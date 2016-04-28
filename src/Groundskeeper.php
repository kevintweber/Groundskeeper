<?php

namespace Groundskeeper;

use Groundskeeper\Tokens\Tokenizer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class Groundskeeper implements LoggerAwareInterface
{
    /** @var Configuration */
    private $configuration;

    /** @var null|LoggerInterface */
    private $logger;

    /**
     * Constructor
     *
     * @param array|Configuration $options
     */
    public function __construct($options = array())
    {
        $this->logger = null;
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
        $tokenContainer = $tokenizer->tokenize($html);

        // Remove unwanted tokens
        $tokenContainer->remove($this->logger);

        // Clean
        $tokenContainer->clean($this->logger);

        // Output
        $outputClassName = 'Groundskeeper\\Output\\' .
            ucfirst($this->configuration->get('output'));
        $output = new $outputClassName();

        return $output($tokenContainer);
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
