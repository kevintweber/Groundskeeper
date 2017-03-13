<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\NonParticipating;
use Psr\Log\LoggerInterface;

/**
 * "select" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-select-element
 */
class Select extends OpenElement implements FlowContent, InteractiveContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $selectAllowedAttributes = array(
            '/^autocomplete$/i' => Attribute::CS_STRING,
            '/^autofocus$/i' => Attribute::BOOL,
            '/^disabled$/i' => Attribute::BOOL,
            '/^form$/i' => Attribute::CS_STRING,
            '/^multiple$/i' => Attribute::BOOL,
            '/^name$/i' => Attribute::CS_STRING,
            '/^type$/i' => Attribute::CI_ENUM . '("submit","reset","button","menu"|"submit")',
            '/^value$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $selectAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating ||
                $child instanceof Option ||
                $child instanceof Optgroup ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only "option, "optgroup", and script supporting elements allowed as children of a "select" element.');
            $this->removeChild($child);
        }
    }

    public function isInteractiveContent()
    {
        return true;
    }
}
