<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "ruby" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-ruby-element
 */
class Ruby extends OpenElement implements FlowContent, PhrasingContent
{
}
