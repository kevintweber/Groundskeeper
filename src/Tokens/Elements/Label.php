<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Psr\Log\LoggerInterface;

/**
 * "label" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-label-element
 */
class Label extends OpenElement implements FlowContent, InteractiveContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $labelAllowedAttributes = array(
            '/^for$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $labelAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger) : bool
    {
        $label = new self($this->configuration, 0, 0, 'label');
        if ($this->hasAncestor($label)) {
            $logger->debug('Removing ' . $this . '. Cannot be have "label" element ancestor.');

            return true;
        }

        return false;
    }

    public function isInteractiveContent() : bool
    {
        return true;
    }
}
