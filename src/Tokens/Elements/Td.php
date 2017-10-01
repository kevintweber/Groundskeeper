<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningRoot;
use Psr\Log\LoggerInterface;

/**
 * "td" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-td-element
 */
class Td extends OpenElement implements SectioningRoot
{
    protected function getAllowedAttributes()
    {
        $tdAllowedAttributes = array(
            '/^colspan$/i' => Attribute::INT,
            '/^rowspan$/i' => Attribute::INT,
            '/^headers$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $tdAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger) : bool
    {
        // "tr" must be parent.
        $parent = $this->getParent();
        if ($parent !== null && !$parent instanceof Tr) {
            $logger->debug('Removing ' . $this . '. Must be a child of "tr" element.');

            return true;
        }

        return false;
    }
}
