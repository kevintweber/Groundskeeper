<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InlineElement;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\TransparentElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "a" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-a-element
 */
class A extends OpenElement implements FlowContent, InteractiveContent, PhrasingContent, InlineElement, TransparentElement
{
    protected function getAllowedAttributes()
    {
        $aAllowedAttributes = array(
            '/^href$/i' => Element::ATTR_URI,
            '/^target$/i' => Element::ATTR_CS_STRING,
            '/^download$/i' => Element::ATTR_CS_STRING,
            '/^ping$/i' => Element::ATTR_URI,
            '/^rel$/i' => Element::ATTR_CS_STRING,
            '/^hreflang$/i' => Element::ATTR_CS_STRING,
            '/^type$/i' => Element::ATTR_CS_STRING,
            '/^referrerpolicy$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $aAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function fixSelf(LoggerInterface $logger)
    {
        // If the "itemprop" attribute is specified on an "a" element, then
        // the "href" attribute must also be specified.
        if ($this->hasAttribute('itemprop') && !$this->hasAttribute('href')) {
            $logger->debug($this . ' with "itemprop" attribute requires the "href" attribute also.  Adding empty "href" attribute.');
            $this->addAttribute('href', '');
        }
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        // There must be no interactive content or "a" element descendants.
        foreach ($this->children as $child) {
            if ($child->getType() == Token::COMMENT) {
                continue;
            }

            if ($child->getType() == Token::ELEMENT &&
                ($child->getName() == 'a' ||
                 ($child instanceof InteractiveContent && $child->isInteractiveContent()))) {
                $logger->debug('Removing ' . $child . '. Element "a" cannot contain "a" or interactive content elements.');
                $this->removeChild($child);
            }
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // The target, download, ping, rel, hreflang, type, and
        // referrerpolicy attributes must be omitted if the href
        // attribute is not present.
        if (!$this->hasAttribute('href')) {
            if ($this->hasAttribute('target') ||
                $this->hasAttribute('download') ||
                $this->hasAttribute('ping') ||
                $this->hasAttribute('rel') ||
                $this->hasAttribute('hreflang') ||
                $this->hasAttribute('type') ||
                $this->hasAttribute('referrerpolicy')
            ) {
                $logger->debug('Removing invalid attributes. ' . $this . ' without "href" attribute cannot contain "target", "download", "ping", "rel", "hreflang", "type", or "referrerpolicy" attributes.');
                $this->removeAttribute('target');
                $this->removeAttribute('download');
                $this->removeAttribute('ping');
                $this->removeAttribute('rel');
                $this->removeAttribute('hreflang');
                $this->removeAttribute('type');
                $this->removeAttribute('referrerpolicy');
            }
        }

        return false;
    }

    public function isInteractiveContent()
    {
        return $this->hasAttribute('href');
    }

    public function isTransparentElement()
    {
        return true;
    }
}
