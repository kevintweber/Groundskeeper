<?php

namespace Groundskeeper\Tests;

use Psr\Log\AbstractLogger;

class TestableLogger extends AbstractLogger
{
    /** @var array */
    private $logs;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->logs = array();
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param array  $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        $this->logs[] = array(
            'level' => $level,
            'message' => $message,
            'context' => $context
        );
    }

    public function getLogs()
    {
        return $this->logs;
    }
}
