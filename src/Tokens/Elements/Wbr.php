<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "wbr" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-wbr-element
 */
class Wbr extends ClosedElement implements FlowContent, PhrasingContent
{

}
