<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;

/**
 * "input" element
 *
 * https://html.spec.whatwg.org/multipage/forms.html#the-input-element
 */
class Input extends ClosedElement implements FlowContent, InteractiveContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $inputAllowedAttributes = array(
            '/^accept$/i' => Attribute::CI_STRING,
            '/^alt$/i' => Attribute::CS_STRING,
            '/^autocomplete$/i' => Attribute::CS_STRING,
            '/^autofocus$/i' => Attribute::BOOL,
            '/^checked$/i' => Attribute::BOOL,
            '/^dirname$/i' => Attribute::CS_STRING,
            '/^disabled$/i' => Attribute::BOOL,
            '/^form$/i' => Attribute::CS_STRING,
            '/^formaction$/i' => Attribute::URI,
            '/^formenctype$/i' => Attribute::CS_STRING,
            '/^formmethod$/i' => Attribute::CI_ENUM . '("","get","post","dialog"|"get")',
            '/^formnovalidate$/i' => Attribute::BOOL,
            '/^formtarget$/i' => Attribute::CS_STRING,
            '/^height$/i' => Attribute::INT,
            '/^inputmode$/i' => Attribute::CI_STRING,
            '/^list$/i' => Attribute::CS_STRING,
            '/^max$/i' => Attribute::CS_STRING,
            '/^maxlength$/i' => Attribute::INT,
            '/^min$/i' => Attribute::CS_STRING,
            '/^minlength$/i' => Attribute::INT,
            '/^multiple$/i' => Attribute::BOOL,
            '/^name$/i' => Attribute::CS_STRING,
            '/^pattern$/i' => Attribute::CS_STRING,
            '/^placeholder$/i' => Attribute::CS_STRING,
            '/^readonly$/i' => Attribute::BOOL,
            '/^required$/i' => Attribute::BOOL,
            '/^size$/i' => Attribute::INT,
            '/^src$/i' => Attribute::URI,
            '/^step$/i' => Attribute::CI_STRING,
            '/^type$/i' => Attribute::CI_ENUM . '("hidden","text","search","tel","url","email","password","date","month","week","time","datetime-local","number","range","color","checkbox","radio","file","submit","image","reset","button"|"text")',
            '/^value$/i' => Attribute::CS_STRING,
            '/^width$/i' => Attribute::INT
        );

        return array_merge(
            $inputAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function isInteractiveContent()
    {
        if (!$this->hasAttribute('type')) {
            return true;
        }

        return $this->getAttribute('type') !== 'hidden';
    }
}
