<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
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
            '/^alt$/i' => Attribute::CS_STRING,
            '/^src$/i' => Attribute::URI,
            '/^srcset$/i' => Attribute::CS_STRING,
            '/^sizes$/i' => Attribute::CS_STRING,
            '/^crossorigin$/i' => Attribute::CS_STRING,
            '/^usemap$/i' => Attribute::CS_STRING,
            '/^ismap$/i' => Attribute::CS_STRING,
            '/^width$/i' => Attribute::INT,
            '/^height$/i' => Attribute::INT,
            '/^referrerpolicy$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $imgAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
