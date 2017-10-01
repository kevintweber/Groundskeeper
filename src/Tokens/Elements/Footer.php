<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "footer" element
 */
class Footer extends OpenElement implements FlowContent
{
    protected function removeInvalidSelf(LoggerInterface $logger) : bool
    {
        $footer = new self($this->configuration, 0, 0, 'footer');
        $header = new Header($this->configuration, 0, 0, 'header');
        $main = new Main($this->configuration, 0, 0, 'main');
        if ($this->hasAncestor($footer) ||
            $this->hasAncestor($header) ||
            $this->hasAncestor($main)) {
            $logger->debug('Removing ' . $this . '. Element "footer" should not be a descendant of "footer", "header", or "main" elements.');

            return true;
        }

        return false;
    }
}
