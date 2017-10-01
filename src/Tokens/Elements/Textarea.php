<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\NonParticipating;
use Groundskeeper\Tokens\Text;
use Psr\Log\LoggerInterface;

/**
 * "textarea" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-textarea-element
 */
class Textarea extends OpenElement implements FlowContent, InteractiveContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $textareaAllowedAttributes = array(
            '/^autocomplete$/i' => Attribute::CS_STRING,
            '/^autofocus$/i' => Attribute::BOOL,
            '/^cols$/i' => Attribute::INT,
            '/^dirname$/i' => Attribute::CS_STRING,
            '/^disabled$/i' => Attribute::BOOL,
            '/^form$/i' => Attribute::CS_STRING,
            '/^inputmode$/i' => Attribute::CS_STRING,
            '/^maxlength$/i' => Attribute::CS_STRING,
            '/^minlength$/i' => Attribute::CS_STRING,
            '/^name$/i' => Attribute::CS_STRING,
            '/^placeholder$/i' => Attribute::CS_STRING,
            '/^readonly$/i' => Attribute::BOOL,
            '/^required$/i' => Attribute::BOOL,
            '/^rows$/i' => Attribute::INT,
            '/^wrap$/i' => Attribute::CI_ENUM . '("","soft","hard"|"soft")'
        );

        return array_merge(
            $textareaAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating ||
                $child instanceof Text) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only text is allowed as children of a "textarea" element.');
            $this->removeChild($child);
        }
    }

    public function isInteractiveContent() : bool
    {
        return true;
    }
}
