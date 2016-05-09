<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
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
            '/^name$/i' => Element::ATTR_CS_STRING,
            '/^value$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $paramAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            // Must be child of "object" element.
            $parent = $this->getParent();
            if ($parent !== null && $parent->getName() !== 'object') {
                $logger->debug('Element "param" must be a child of "object" element.');

                return false;
            }
        }

        return true;
    }
}
