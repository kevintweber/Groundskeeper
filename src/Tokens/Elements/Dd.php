<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "dd" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-dd-element
 */
class Dd extends OpenElement
{
    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // Must be child of "dl" element.
        $parent = $this->getParent();
        if ($parent !== null &&
            $parent->getName() != 'dl') {
            $logger->debug($this . ' must be a child of a "dl" element.');

            return true;
        }

        return false;
    }
}
