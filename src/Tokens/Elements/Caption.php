<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "caption" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-caption-element
 */
class Caption extends OpenElement implements FlowContent
{
    /**
     * @todo Caption must be *first* child of table.
     */
    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        $parent = $this->getParent();
        if ($parent !== null &&
            !($parent instanceof Table)) {
            $logger->debug('Removing ' . $this . '. Must be child of "table" element.');

            return true;
        }

        return false;
    }
}
