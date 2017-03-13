<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
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
            '/^accept-charset$/i' => Attribute::CS_STRING,
            '/^action$/i' => Attribute::URI,
            '/^autocomplete$/i' => Attribute::CI_ENUM . '("","on","off")',
            '/^enctype$/i' => Attribute::CS_STRING,
            '/^method$/i' => Attribute::CI_ENUM . '("","get","post","dialog"|"get")',
            '/^name$/i' => Attribute::CS_STRING,
            '/^novalidate$/i' => Attribute::BOOL,
            '/^target$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $formAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        $form = new self($this->configuration, 0, 0, 'form');
        if ($this->hasAncestor($form)) {
            $logger->debug('Removing ' . $this . '. Cannot have a "form" element ancestor.');

            return true;
        }

        return false;
    }
}
