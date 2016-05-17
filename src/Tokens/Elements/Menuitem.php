<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\OpenElement;

/**
 * "menuitem" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-menuitem-element
 *
 * @todo Implement checks.
 */
class Menuitem extends OpenElement
{
    protected function getAllowedAttributes()
    {
        $menuitemAllowedAttributes = array(
            '/^type$/i' => Attribute::CI_ENUM . '("","command","checkbox","radio"|"command")',
            '/^label$/i' => Attribute::CS_STRING,
            '/^icon$/i' => Attribute::CS_STRING,
            '/^disabled$/i' => Attribute::BOOL,
            '/^checked$/i' => Attribute::BOOL,
            '/^radiogroup$/i' => Attribute::CS_STRING,
            '/^default$/i' => Attribute::CS_STRING
        );

        return array_merge(
            $menuitemAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }
}
