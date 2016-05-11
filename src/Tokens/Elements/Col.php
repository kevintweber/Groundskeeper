<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
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
            '/^span$/i' => Element::ATTR_INT,
        );

        return array_merge(
            $colAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            // "colgroup" must be parent.
            $parent = $this->getParent();
            if ($parent !== null && $parent->getName() != 'colgroup') {
                $logger->debug('Element "col" must be a child of the "colgroup" element.');

                return false;
            }
        }

        return true;
    }
}
