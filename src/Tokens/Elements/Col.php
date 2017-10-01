<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Psr\Log\LoggerInterface;

/**
 * "col" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-col-element
 */
class Col extends ClosedElement
{
    protected function getAllowedAttributes()
    {
        $colAllowedAttributes = array(
            '/^span$/i' => Attribute::INT,
        );

        return array_merge(
            $colAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger) : bool
    {
        // "colgroup" must be parent.
        $parent = $this->getParent();
        if ($parent !== null && !$parent instanceof Colgroup) {
            $logger->debug('Removing ' . $this . '. Must be a child of the "colgroup" element.');

            return true;
        }

        return false;
    }
}
