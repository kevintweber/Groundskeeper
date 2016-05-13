<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Psr\Log\LoggerInterface;

/**
 * "form" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-form-element
 */
class Form extends OpenElement
{
    protected function getAllowedAttributes()
    {
        $formAllowedAttributes = array(
            '/^accept-charset$/i' => Element::ATTR_CS_STRING,
            '/^action$/i' => Element::ATTR_URI,
            '/^autocomplete$/i' => Element::ATTR_CI_ENUM . '("","on","off")',
            '/^enctype$/i' => Element::ATTR_CS_STRING,
            '/^method$/i' => Element::ATTR_CI_ENUM . '("","get","post","dialog"|"get")',
            '/^name$/i' => Element::ATTR_CS_STRING,
            '/^novalidate$/i' => Element::ATTR_BOOL,
            '/^target$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $formAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        $form = new self($this->configuration, 'form');
        if ($this->hasAncestor($form)) {
            $logger->debug($this . ' cannot be have a "form" element ancestor.');

            return true;
        }

        return false;
    }
}
