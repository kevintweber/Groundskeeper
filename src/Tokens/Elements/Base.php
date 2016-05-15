<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Psr\Log\LoggerInterface;

/**
 * "base" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-base-element
 */
class Base extends ClosedElement implements MetadataContent
{
    protected function getAllowedAttributes()
    {
        $baseAllowedAttributes = array(
            '/^href$/i' => Attribute::URI,
            '/^target$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $baseAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // "base" element must be child of "head" element.
        if ($this->getParent() !== null && !($this->getParent() instanceof Head)) {
            $logger->debug('Removing ' . $this . '. Must be a "head" element child.');

            return true;
        }

        // Must have either "href" or "target" attribute or both.
        if (!$this->hasAttribute('href') && !$this->hasAttribute('target')) {
            $logger->debug('Removing ' . $this . '. Must have either the "href" or "target" attribute or both.');

            return true;
        }

        return false;
    }
}
