<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "datalist" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-datalist-element
 */
class Datalist extends OpenElement implements FlowContent, PhrasingContent
{
}
