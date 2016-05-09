<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\EmbeddedContent;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "embed" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-embed-element
 */
class Embed extends ClosedElement implements FlowContent, PhrasingContent, EmbeddedContent, InteractiveContent
{
    protected function getAllowedAttributes()
    {
        $embedAllowedAttributes = array(
            '/^src$/i' => Element::ATTR_URI,
            '/^type$/i' => Element::ATTR_CS_STRING,
            '/^name$/i' => Element::ATTR_CS_STRING,
            '/^[a-z_-]$/i' => Element::ATTR_CS_STRING,
        );

        return array_merge(
            $embedAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function isInteractiveContent()
    {
        return true;
    }
}
