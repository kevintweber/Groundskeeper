<?php

namespace Groundskeeper\Tokens;

use Psr\Log\LoggerInterface;

/**
 * The remove function will handle the blacklisted tokens and elements.
 */
interface Removable
{
    /**
     * Will remove any undesired tokens.
     *
     * @param LoggerInterface $logger Will log any removal actions.
     */
    public function remove(LoggerInterface $logger = null);
}
