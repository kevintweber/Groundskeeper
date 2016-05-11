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

    protected function doClean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            // "tr" must be parent.
            $parent = $this->getParent();
            if ($parent !== null &&
                $parent->getName() != 'tr') {
                $logger->debug('Element "th" must be a child of "tr" element.');

                return false;
            }
        }

        return true;
    }
}
