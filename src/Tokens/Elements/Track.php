<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Psr\Log\LoggerInterface;

/**
 * "track" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-track-element
 */
class Track extends ClosedElement
{
    protected function getAllowedAttributes()
    {
        $trackAllowedAttributes = array(
            '/^kind$/i' => Attribute::CS_STRING,
            '/^src$/i' => Attribute::URI,
            '/^srclang$/i' => Attribute::CS_STRING,
            '/^label$/i' => Attribute::CS_STRING,
            '/^default$/i' => Attribute::BOOL
        );

        return array_merge(
            $trackAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // Must be child of "object" element.
        $parent = $this->getParent();
        if ($parent !== null &&
            !$parent instanceof Video &&
            !$parent instanceof Audio) {
            $logger->debug('Removing ' . $this . '. Must be a child of "video" or "audio" element.');

            return true;
        }

        return false;
    }
}
