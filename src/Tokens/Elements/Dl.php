<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\NonParticipating;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "dl" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-dl-element
 */
class Dl extends OpenElement implements FlowContent
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // Only "dd", "dt", and ScriptSupporting elements allowed.
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating ||
                $child instanceof Dd ||
                $child instanceof Dt ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only elements "dd", "dt", and script supporting elements allowed as children of "dl" element.');
            $this->removeChild($child);
        }
    }
}
