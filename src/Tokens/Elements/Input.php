<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Psr\Log\LoggerInterface;

/**
 * "input" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-input-element
 */
class Input extends ClosedElement implements FlowContent, InteractiveContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $inputAllowedAttributes = array(
            '/^accept$/i' => Element::ATTR_CI_STRING,
            '/^alt$/i' => Element::ATTR_CS_STRING,
            '/^autocomplete$/i' => Element::ATTR_CS_STRING,
            '/^autofocus$/i' => Element::ATTR_BOOL,
            '/^checked$/i' => Element::ATTR_BOOL,
            '/^dirname$/i' => Element::ATTR_CS_STRING,
            '/^disabled$/i' => Element::ATTR_CS_STRING,
            '/^form$/i' => Element::ATTR_CS_STRING,
            '/^formaction$/i' => Element::ATTR_URI,
            '/^formenctype$/i' => Element::ATTR_CS_STRING,
            '/^formmethod$/i' => Element::ATTR_CI_ENUM . '("","get","post","dialog"|"get")',
            '/^formnovalidate$/i' => Element::ATTR_BOOL,
            '/^formtarget$/i' => Element::ATTR_CS_STRING,
            '/^height$/i' => Element::ATTR_INT,
            '/^inputmode$/i' => Element::ATTR_CI_STRING,
            '/^list$/i' => Element::ATTR_CS_STRING,
            '/^max$/i' => Element::ATTR_CS_STRING,
            '/^maxlength$/i' => Element::ATTR_INT,
            '/^min$/i' => Element::ATTR_CS_STRING,
            '/^minlength$/i' => Element::ATTR_INT,
            '/^multiple$/i' => Element::ATTR_BOOL,
            '/^name$/i' => Element::ATTR_CS_STRING,
            '/^pattern$/i' => Element::ATTR_CS_STRING,
            '/^placeholder$/i' => Element::ATTR_CS_STRING,
            '/^readonly$/i' => Element::ATTR_BOOL,
            '/^required$/i' => Element::ATTR_BOOL,
            '/^size$/i' => Element::ATTR_INT,
            '/^src$/i' => Element::ATTR_URI,
            '/^step$/i' => Element::ATTR_CI_STRING,
            '/^type$/i' => Element::ATTR_CI_ENUM . '("hidden","text","search","tel","url","email","password","date","month","week","time","datetime-local","number","range","color","checkbox","radio","file","submit","image","reset","button"|"text")',
            '/^value$/i' => Element::ATTR_CS_STRING,
            '/^width$/i' => Element::ATTR_INT
        );

        return array_merge(
            $inputAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        $input = new self($this->configuration, 'input');
        if ($this->hasAncestor($input)) {
            $logger->debug('Removing ' . $this . '. Cannot be have "input" element ancestor.');

            return true;
        }

        return false;
    }

    public function isInteractiveContent()
    {
        if (!$this->hasAttribute('type')) {
            return true;
        }

        return $this->getAttribute('type') !== 'hidden';
    }
}
