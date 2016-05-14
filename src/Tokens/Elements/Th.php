<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "th" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-th-element
 */
class Th extends OpenElement
{
    protected function getAllowedAttributes()
    {
        $thAllowedAttributes = array(
            '/^colspan$/i' => Element::ATTR_INT,
            '/^rowspan$/i' => Element::ATTR_INT,
            '/^headers$/i' => Element::ATTR_CS_STRING,
            '/^scope$/i' => Element::ATTR_CS_STRING,
            '/^abbr$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $thAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
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
