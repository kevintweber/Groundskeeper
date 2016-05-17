<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningContent;
use Psr\Log\LoggerInterface;

/**
 * "aside" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-aside-element
 */
class Aside extends OpenElement implements FlowContent, SectioningContent
{
    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        /// @todo Remove "main" element descendents.
    }
}
