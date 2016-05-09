<?php

namespace Groundskeeper\Tokens\Elements;

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
            '/^src$/i' => Element::ATTR_URI,
            '/^srcdoc$/i' => Element::ATTR_CS_STRING,
            '/^name$/i' => Element::ATTR_CS_STRING,
            '/^sandbox$/i' => Element::ATTR_CS_STRING,
            '/^allowfullscreen$/i' => Element::ATTR_CS_STRING,
            '/^width$/i' => Element::ATTR_INT,
            '/^height$/i' => Element::ATTR_INT,
            '/^referrerpolicy$/i' => Element::ATTR_CS_STRING,
        );

        return array_merge(
            $iframeAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function isInteractiveContent()
    {
        return true;
    }
}
