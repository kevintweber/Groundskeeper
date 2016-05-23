<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\EmbeddedContent;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "canvas" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-canvas-element
 */
class Canvas extends OpenElement implements FlowContent, PhrasingContent, EmbeddedContent
{
    protected function getAllowedAttributes()
    {
        $canvasAllowedAttributes = array(
            '/^width$/i' => Attribute::INT,
            '/^height$/i' => Attribute::INT
        );

        return array_merge(
            $canvasAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
