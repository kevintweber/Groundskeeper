<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "main" element
 */
class Main extends OpenElement implements FlowContent
{
    protected function doClean(LoggerInterface $logger)
    {
        $footer = new Footer($this->configuration, 'footer');
        $header = new Header($this->configuration, 'header');
        $main = new Main($this->configuration, 'main');
        if ($this->hasAncestor($footer) ||
            $this->hasAncestor($header) ||
            $this->hasAncestor($main)) {
            $logger->debug('Removing ' . $this . '. Element "main" should not be a descendant of "footer", "header", or "main" elements.');

            return false;
        }

        return true;
    }
}
