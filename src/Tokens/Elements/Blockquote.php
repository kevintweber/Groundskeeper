<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningRoot;

/**
 * "blockquote" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-blockquote-element
 */
class Blockquote extends OpenElement implements FlowContent, SectioningRoot
{
    protected function getAllowedAttributes()
    {
        $blockquoteAllowedAttributes = array(
            '/^cite$/i' => Attribute::URI
        );

        return array_merge(
            $blockquoteAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
