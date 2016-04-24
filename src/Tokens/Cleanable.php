<?php

namespace Groundskeeper\Tokens;

use Psr\Log\LoggerInterface;

interface Cleanable
{
    /**
     * Will attempt to clean itself according to the configuration.
     *
     * @param LoggerInterface $logger Will log any cleaning actions.
     */
    public function clean(LoggerInterface $logger = null);
}
