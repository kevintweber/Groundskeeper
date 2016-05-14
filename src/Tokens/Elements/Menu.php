<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\NonParticipating;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "menu" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-menu-element
 */
class Menu extends OpenElement implements FlowContent
{
    protected function getAllowedAttributes()
    {
        $menuAllowedAttributes = array(
            '/^type$/i' => Element::ATTR_CI_ENUM . '("","context","toolbar"|"context")',
            '/^label$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $menuAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function fixSelf(LoggerInterface $logger)
    {
        if (!$this->hasAttribute('type')) {
            $logger->debug('Modifying ' . $this . '. Adding the default "type" attribute for the "menu" element.');
            $this->addAttribute('type', 'context');
        }
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // Only "li" and ScriptSupporting elements allowed.
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating) {
                continue;
            }

            if (!$child instanceof Li &&
                !$child instanceof Menuitem &&
                !$child instanceof Hr &&
                !$child instanceof Menu &&
                !$child instanceof ScriptSupporting) {
                $logger->debug('Removing ' . $child . '. Only elements "li", "menuitem", "hr", "menu", and script supporting elements allowed as children of "menu" element.');
                $this->removeChild($child);
            }
        }
    }
}
