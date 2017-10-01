<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Psr\Log\LoggerInterface;

/**
 * "param" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-param-element
 */
class Param extends ClosedElement
{
    protected function getAllowedAttributes()
    {
        $paramAllowedAttributes = array(
            '/^name$/i' => Attribute::CS_STRING,
            '/^value$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $paramAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger) : bool
    {
        // Must be child of "object" element.
        $parent = $this->getParent();
        if ($parent !== null && !$parent instanceof Object) {
            $logger->debug('Removing ' . $this . '. Must be a child of "object" element.');

            return true;
        }

        return false;
    }
}
