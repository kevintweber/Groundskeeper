<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Psr\Log\LoggerInterface;

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
        // BASE must be child of HEAD.
        if ($this->getParent() !== null && $this->getParent()->getName() !== 'head') {
            return false;
        }

        // Must have either "href" or "target" attribute or both.
        if (!$this->hasAttribute('href') && !$this->hasAttribute('target')) {
            return false;
        }

        return true;
    }
}
