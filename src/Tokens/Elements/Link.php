<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;

class Link extends ClosedElement implements MetadataContent
{
    protected function getAllowedAttributes()
    {
        $linkAllowedAttributes = array(
            '/^href$/i' => Element::ATTR_URI,
            '/^crossorigin$/i' => Element::ATTR_CS_STRING,
            '/^rel$/i' => Element::ATTR_CS_STRING,
            '/^media$/i' => Element::ATTR_CS_STRING,
            '/^hreflang$/i' => Element::ATTR_CS_STRING,
            '/^type$/i' => Element::ATTR_CS_STRING,
            '/^sizes$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $linkAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger = null)
    {
        // Must have "href" attribute.
        if (!$this->hasAttribute('href')) {
            return false;
        }

        // Must have either "rel" or "itemprop" attribute, but not both.
        /// @todo

        return true;
    }
}
