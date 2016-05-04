<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Psr\Log\LoggerInterface;

class Meta extends ClosedElement implements MetadataContent
{
    protected function getAllowedAttributes()
    {
        $metaAllowedAttributes = array(
            '/^name$/i' => Element::ATTR_CS_STRING,
            '/^http-equiv$/i' => Element::ATTR_CI_ENUM . '("content-language","content-type","default-style","refresh","set-cookie","x-ua-compatible","content-security-policy")',
            '/^content$/i' => Element::ATTR_CS_STRING,
            '/^charset$/i' => Element::ATTR_CI_STRING,
            '/^property$/i' => Element::ATTR_CS_STRING  // Facebook OG attribute name.
        );

        return array_merge(
            $metaAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger)
    {
        // If "charset" attribute is present, the must be child of "head" element.
        if ($this->hasAttribute('charset') && $this->getParent() !== null) {
            if ($this->getParent()->getType() !== Token::ELEMENT || $this->getParent()->getName() != 'head') {
                $logger->debug('Element "meta" with a "charset" attribute must be a "head" element child.');

                return false;
            }
        }

        return true;
    }
}
