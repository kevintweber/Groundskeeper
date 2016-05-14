<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "rt" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-rt-element
 */
class Rt extends OpenElement
{
    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // Must be child of "ruby" element.
        $parent = $this->getParent();
        if ($parent !== null && !$parent instanceof Ruby) {
            $logger->debug('Removing ' . $this . '. Must be a child of a "ruby" element.');

            return true;
        }

        return false;
    }
}
