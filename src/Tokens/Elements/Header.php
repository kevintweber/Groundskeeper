<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "header" element
 */
class Header extends OpenElement implements FlowContent
{
    protected function doClean(LoggerInterface $logger = null)
    {
        $footer = new Footer($this->configuration, 'footer');
        $header = new Header($this->configuration, 'header');
        $main = new Main($this->configuration, 'main');
        if ($this->hasAncestor($footer) ||
            $this->hasAncestor($header) ||
            $this->hasAncestor($main)) {
            if ($logger !== null) {
                $logger->debug('Removing ' . $this . '. Element "header" should not be a descendant of "footer", "header", or "main" elements.');
            }

            return false;
        }

        return true;
    }
}
