<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\MediaElement;
use Psr\Log\LoggerInterface;

/**
 * "source" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-source-element
 */
class Source extends ClosedElement
{
    protected function getAllowedAttributes()
    {
        $sourceAllowedAttributes = array(
            '/^src$/i' => Attribute::URI,
            '/^type$/i' => Attribute::CS_STRING,
            '/^srcset$/i' => Attribute::CS_STRING,
            '/^sizes$/i' => Attribute::CS_STRING,
            '/^media$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $sourceAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger) : bool
    {
        // Child of "picture" element.
        // Child of media element.
        $parent = $this->getParent();
        if ($parent !== null &&
            !$parent instanceof Picture &&
            !$parent instanceof MediaElement) {
            $logger->debug('Removing ' . $this . ' must be a child of "picture" element or a media element.');

            return true;
        }

        return false;
    }
}
