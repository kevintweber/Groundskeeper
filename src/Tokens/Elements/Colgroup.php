<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\Element;

/**
 * "colgroup" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-colgroup-element
 */
class Colgroup extends Element
{
    protected function getAllowedAttributes()
    {
        $colgroupAllowedAttributes = array(
            '/^span$/i' => Attribute::INT
        );

        return array_merge(
            $colgroupAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
