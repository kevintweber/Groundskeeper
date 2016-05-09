<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\ElementTypes\ClosedElement;

/**
 * "img" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-img-element
 *
 * @todo Finish attributes and doClean.
 */
class Img extends ClosedElement
{
    protected function getAllowedAttributes()
    {
        $imgAllowedAttributes = array(
            '/^alt$/i' => Element::ATTR_CS_STRING,
            '/^src$/i' => Element::ATTR_URI,
            '/^srcset$/i' => Element::ATTR_CS_STRING,
            '/^sizes$/i' => Element::ATTR_CS_STRING,
            '/^crossorigin$/i' => Element::ATTR_CS_STRING,
            '/^usemap$/i' => Element::ATTR_CS_STRING,
            '/^ismap$/i' => Element::ATTR_CS_STRING,
            '/^width$/i' => Element::ATTR_INT,
            '/^height$/i' => Element::ATTR_INT,
            '/^referrerpolicy$/i' => Element::ATTR_CS_STRING,
        );

        return array_merge(
            $imgAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
