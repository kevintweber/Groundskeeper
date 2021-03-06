<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "rp" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-rp-element
 */
class Rp extends OpenElement
{
    protected function removeInvalidSelf(LoggerInterface $logger) : bool
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
