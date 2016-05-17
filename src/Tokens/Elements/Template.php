<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;

/**
 * "template" element
 *
 * https://html.spec.whatwg.org/multipage/scripting.html#the-template-element
 */
class Template extends OpenElement implements FlowContent, MetadataContent, PhrasingContent, ScriptSupporting
{
}
