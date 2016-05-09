<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "br" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-br-element
 */
class Br extends ClosedElement implements FlowContent, PhrasingContent
{
}
