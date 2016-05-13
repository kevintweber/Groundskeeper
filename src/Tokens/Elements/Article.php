<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningContent;

/**
 * "article" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-article-element
 */
class Article extends OpenElement implements FlowContent, SectioningContent
{
}
