<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
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
        $parent = $this->getParent();
        if ($parent == null) {
            return parent::getAllowedAttributes();
        }

        if ($parent->getType() === Token::ELEMENT &&
            $parent->getName() == 'ol') {
            $liAllowedAttributes = array(
                '/^value$/i' => Element::ATTR_INT
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
        if ($parent === null) {
            return false;
        }

        if ($parent->getName() == 'ol' || $parent->getName() == 'ul') {
            return false;
        }

        if ($parent->getName() == 'menu' && $parent->hasAttribute('type')) {
            $typeAttributeValue = $parent->getAttribute('type');
            if ($typeAttributeValue == 'toolbar') {
                return false;
            }
        }

        $logger->debug($this . ' only allowed inside "ol", "ul" and "menu[type=toolbar]" elements.');

        return true;
    }
}
