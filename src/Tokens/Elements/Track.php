<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
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
            '/^kind$/i' => Element::ATTR_CS_STRING,
            '/^src$/i' => Element::ATTR_URI,
            '/^srclang$/i' => Element::ATTR_CS_STRING,
            '/^label$/i' => Element::ATTR_CS_STRING,
            '/^default$/i' => Element::ATTR_BOOL
        );

        return array_merge(
            $trackAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            // Must be child of "object" element.
            $parent = $this->getParent();
            if ($parent !== null &&
                $parent->getName() !== 'video' &&
                $parent->getName() !== 'audio') {
                $logger->debug('Element "track" must be a child of "video" or "audio" element.');

                return false;
            }
        }

        return true;
    }
}
