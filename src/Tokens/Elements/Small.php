<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InlineElement;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "small" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-small-element
 */
class Small extends OpenElement implements FlowContent, PhrasingContent, InlineElement
{
}
