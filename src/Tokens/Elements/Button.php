<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Psr\Log\LoggerInterface;

/**
 * "button" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-button-element
 */
class Button extends OpenElement implements FlowContent, InteractiveContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $buttonAllowedAttributes = array(
            '/^autofocus$/i' => Attribute::BOOL,
            '/^disabled$/i' => Attribute::BOOL,
            '/^form$/i' => Attribute::CS_STRING,
            '/^formaction$/i' => Attribute::URI,
            '/^formenctype$/i' => Attribute::CS_STRING,
            '/^formmethod$/i' => Attribute::CI_ENUM . '("","get","post","dialog"|"get")',
            '/^formnovalidate$/i' => Attribute::BOOL,
            '/^formtarget$/i' => Attribute::CS_STRING,
            '/^menu$/i' => Attribute::CS_STRING,
            '/^name$/i' => Attribute::CS_STRING,
            '/^type$/i' => Attribute::CI_ENUM . '("submit","reset","button","menu"|"submit")',
            '/^value$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $buttonAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        foreach ($this->children as $child) {
            if ($child instanceof InteractiveContent &&
                $child->isInteractiveContent()) {
                $logger->debug('Removing ' . $child . '. No interactive content inside a "button" element.');
                $this->removeChild($child);
            }
        }
    }

    public function isInteractiveContent()
    {
        return true;
    }
}
