<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "li" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-li-element
 */
class Li extends OpenElement
{
    protected function getAllowedAttributes()
    {
        // "value" attribute for "li" which are children of "ol" elements.
        if ($this->getParent() instanceof Ol) {
            $liAllowedAttributes = array(
                '/^value$/i' => Attribute::INT
            );

            return array_merge(
                $liAllowedAttributes,
                parent::getAllowedAttributes()
            );
        }

        return parent::getAllowedAttributes();
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // Only allowed:
        // Inside ol elements.
        // Inside ul elements.
        // Inside menu elements whose type attribute is in the toolbar state.
        $parent = $this->getParent();
        if ($parent === null || $parent instanceof Ol || $parent instanceof Ul) {
            return false;
        }

        if ($parent instanceof Menu && $parent->hasAttribute('type')) {
            $typeAttributeValue = $parent->getAttribute('type');
            if ($typeAttributeValue == 'toolbar') {
                return false;
            }
        }

        $logger->debug('Removing ' . $this . '. Only allowed inside the "li" element are "ol", "ul" and "menu[type=toolbar]" elements.');

        return true;
    }
}
