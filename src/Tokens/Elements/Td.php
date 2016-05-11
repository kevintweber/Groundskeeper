<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
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
            '/^colspan$/i' => Element::ATTR_INT,
            '/^rowspan$/i' => Element::ATTR_INT,
            '/^headers$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $tdAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            // "tr" must be parent.
            $parent = $this->getParent();
            if ($parent !== null &&
                $parent->getName() != 'tr') {
                $logger->debug('Element "td" must be a child of "tr" element.');

                return false;
            }
        }

        return true;
    }
}
