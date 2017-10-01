<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\EmbeddedContent;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "iframe" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-iframe-element
 */
class Iframe extends OpenElement implements FlowContent, PhrasingContent, EmbeddedContent, InteractiveContent
{
    protected function getAllowedAttributes()
    {
        $iframeAllowedAttributes = array(
            '/^src$/i' => Attribute::URI,
            '/^srcdoc$/i' => Attribute::CS_STRING,
            '/^name$/i' => Attribute::CS_STRING,
            '/^sandbox$/i' => Attribute::CS_STRING,
            '/^allowfullscreen$/i' => Attribute::CS_STRING,
            '/^width$/i' => Attribute::INT,
            '/^height$/i' => Attribute::INT,
            '/^referrerpolicy$/i' => Attribute::CS_STRING,
        );

        return array_merge(
            $iframeAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function isInteractiveContent() : bool
    {
        return true;
    }
}
