<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Psr\Log\LoggerInterface;

/**
 * "base" element
 */
class Base extends ClosedElement implements MetadataContent
{
    protected function getAllowedAttributes()
    {
        $baseAllowedAttributes = array(
            '/^href$/i' => Element::ATTR_URI,
            '/^target$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $baseAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger = null)
    {
        // "base" element must be child of "head" element.
        if ($this->getParent() !== null && $this->getParent()->getName() !== 'head') {
            if ($logger !== null) {
                $logger->debug('Element "base" must be a "head" element child.');
            }

            return false;
        }

        // Must have either "href" or "target" attribute or both.
        if (!$this->hasAttribute('href') && !$this->hasAttribute('target')) {
            if ($logger !== null) {
                $logger->debug('Element "base" must have either the "href" or "target" attribute or both.');
            }

            return false;
        }

        return true;
    }
}
