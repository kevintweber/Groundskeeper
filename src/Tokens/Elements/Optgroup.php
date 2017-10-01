<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\NonParticipating;
use Psr\Log\LoggerInterface;

/**
 * "optgroup" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-optgroup-element
 */
class Optgroup extends OpenElement
{
    protected function getAllowedAttributes()
    {
        $optgroupAllowedAttributes = array(
            '/^disabled$/i' => Attribute::BOOL,
            '/^label$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $optgroupAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating ||
                $child instanceof Option ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only "option" and script supporting elements allowed as children of a "optgroup" element.');
            $this->removeChild($child);
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger) : bool
    {
        $parent = $this->getParent();
        if ($parent !== null && !$parent instanceof Select) {
            $logger->debug('Removing ' . $this . '. Only "select" element allowed as parent of "optgroup" element.');

            return true;
        }

        return false;
    }
}
