<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Psr\Log\LoggerInterface;

class Meta extends ClosedElement implements MetadataContent
{
    protected function getAllowedAttributes()
    {
        $metaAllowedAttributes = array(
            '/^name$/i' => Attribute::CS_STRING,
            '/^http-equiv$/i' => Attribute::CI_ENUM . '("content-language","content-type","default-style","refresh","set-cookie","x-ua-compatible","content-security-policy")',
            '/^content$/i' => Attribute::CS_STRING,
            '/^charset$/i' => Attribute::CI_STRING,
            '/^property$/i' => Attribute::CS_STRING  // Facebook OG attribute name.
        );

        return array_merge(
            $metaAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function fixSelf(LoggerInterface $logger)
    {
        // "name" attribute requires "content" attribute.
        if ($this->hasAttribute('name') && !$this->hasAttribute('content')) {
            $logger->debug('Modifying ' . $this . '. A "meta" element with a "name" attribute requires a "content" attribute.  Adding empty "content" attribute.');
            $this->addAttribute('content', '');
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // If "charset" attribute is present, the must be child of "head" element.
        if ($this->hasAttribute('charset') && $this->getParent() !== null) {
            if (!$this->getParent() instanceof Head) {
                $logger->debug('Removing ' . $this . '. Element "meta" with a "charset" attribute must be a "head" element child.');

                return true;
            }
        }

        return false;
    }
}
