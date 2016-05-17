<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\NonParticipating;
use Groundskeeper\Tokens\Text;
use Psr\Log\LoggerInterface;

/**
 * "option" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-option-element
 */
class Option extends OpenElement
{
    protected function getAllowedAttributes()
    {
        $optionAllowedAttributes = array(
            '/^disabled$/i' => Attribute::BOOL,
            '/^label$/i' => Attribute::CS_STRING,
            '/^selected$/i' => Attribute::BOOL,
            '/^value$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $optionAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        if ($this->hasAttribute('label')) {
            if ($this->hasAttribute('value')) {
                // If both the "label" and "value" attributes are present,
                // then no content is allowed.
                foreach ($this->children as $child) {
                    if ($child instanceof NonParticipating) {
                        continue;
                    }

                    $logger->debug('Removing ' . $child . '. No content allowed inside an "option" element that contains both "label" and "value" attribute.');
                    $this->removeChild($child);
                }
            } else {
                // If the "label" and not "value" attribute is present,
                // then onyl text is allowed.
                foreach ($this->children as $child) {
                    if ($child instanceof NonParticipating ||
                        $child instanceof Text) {
                        continue;
                    }

                    $logger->debug('Removing ' . $child . '. Only text allowed inside an "option" element that contains only a "label" attribute.');
                    $this->removeChild($child);
                }
            }
        } else {
            // If no "label" attribute is present,
            // then only text is allowed.
            foreach ($this->children as $child) {
                if ($child instanceof NonParticipating ||
                    $child instanceof Text) {
                    continue;
                }

                $logger->debug('Removing ' . $child . '. Only text allowed inside an "option" element that does not contain a "label" attribute.');
                $this->removeChild($child);
            }
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        $parent = $this->getParent();
        if ($parent !== null &&
            !$parent instanceof Select &&
            !$parent instanceof Datalist &&
            !$parent instanceof Optgroup) {
            $logger->debug('Removing ' . $this . '. Only "select", "datalist", and "optgroup" elements allowed as parents of "option" element.');

            return true;
        }

        return false;
    }
}
