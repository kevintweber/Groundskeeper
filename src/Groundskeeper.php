<?php

namespace Groundskeeper;

use Groundskeeper\Tokens\Tokenizer;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Groundskeeper implements LoggerAwareInterface
{
    /** @var Configuration */
    private $configuration;

    /** @var LoggerInterface */
    private $logger;

    /**
     * Constructor
     *
     * @param array|Configuration $options
     */
    public function __construct($options = array())
    {
        $this->logger = new NullLogger();
        if ($options instanceof Configuration) {
            $this->configuration = $options;

            return;
        }

        if (!is_array($options)) {
            throw new \InvalidArgumentException('Invalid option type.');
        }

        $this->configuration = new Configuration($options);
    }

    public function clean(string $html) : string
    {
        // Tokenize.
        $tokenizer = new Tokenizer($this->configuration);
        $tokenContainer = $tokenizer->tokenize($html);

        // Remove unwanted tokens.
        $tokenContainer->remove($this->logger);

        // Build output generator.
        $outputClassName = 'Groundskeeper\\Output\\' .
            ucfirst($this->configuration->get('output'));
        $outputGenerator = new $outputClassName();

        // Clean
        $i = 0;
        $cleanedOutput = '';
        do {
            if ($i > 0) {
                $tokenContainer = $tokenizer->tokenize($cleanedOutput);
            }

            $preCleaningOutput = $outputGenerator($tokenContainer);
            $tokenContainer->clean($this->logger);
            $cleanedOutput = $outputGenerator($tokenContainer);
            ++$i;
        } while ($i < 5 && $preCleaningOutput !== $cleanedOutput);

        return $cleanedOutput;
    }

    public function getConfiguration() : Configuration
    {
        return $this->configuration;
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
