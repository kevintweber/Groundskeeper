<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\EmbeddedContent;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\NonParticipating;
use Psr\Log\LoggerInterface;

/**
 * "picture" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-picture-element
 */
class Picture extends OpenElement implements FlowContent, PhrasingContent, EmbeddedContent
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        $encounteredImgElement = false;
        foreach ($this->children as $child) {
            if ($child instanceof NonParticipating ||
                $child instanceof Source ||
                $child instanceof ScriptSupporting) {
                continue;
            }

            if ($child instanceof Img) {
                if ($encounteredImgElement) {
                    $logger->debug('Removing ' . $child . '. Only one "img" element allowed as child of "picture" element.');
                    $this->removeChild($child);

                    continue;
                }

                $encounteredImgElement = true;

                continue;
            }

            $logger->debug('Removing ' . $child . '. Only one "img" element and zero to many "source" elements allowed as children of the "picture" element.');
            $this->removeChild($child);
        }
    }
}
