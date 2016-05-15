<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Psr\Log\LoggerInterface;

class Link extends ClosedElement implements MetadataContent
{
    protected function getAllowedAttributes()
    {
        $linkAllowedAttributes = array(
            '/^href$/i' => Attribute::URI,
            '/^crossorigin$/i' => Attribute::CS_STRING,
            '/^rel$/i' => Attribute::CI_SSENUM . '("alternate","author","help","icon","license","next","pingback","prefetch","prev","search","stylesheet")',
            '/^media$/i' => Attribute::CI_STRING,
            '/^hreflang$/i' => Attribute::CS_STRING,
            '/^type$/i' => Attribute::CI_STRING,
            '/^sizes$/i' => Attribute::CI_STRING
        );

        return array_merge(
            $linkAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // Must have "href" attribute.
        if (!$this->hasAttribute('href')) {
            $logger->debug('Removing ' . $this . '. Requires "href" attribute.');

            return true;
        }

        // Must have either "rel" or "itemprop" attribute, but not both.
        $attrCount = 0;
        foreach ($this->attributes as $attribute) {
            if ($attribute->getName() == 'rel' ||
                $attribute->getName() == 'itemprop') {
                ++$attrCount;
            }

            if ($attrCount > 1) {
                // If both, then we don't know which one should be kept,
                // so we recommend to delete the entire element.
                $logger->debug('Removing ' . $this . '. Requires either "rel" or "itemprop" attribute, but not both.');

                return true;
            }
        }

        if ($attrCount == 0) {
            $logger->debug('Removing ' . $this . '. Requires either "rel" or "itemprop" attribute.');

            return true;
        }

        // If inside "body" element, then we check if allowed.
        $body = new Body($this->configuration, 'body');
        if ($this->hasAncestor($body) && !$this->isAllowedInBody()) {
            $logger->debug('Removing ' . $this . '. Does not have the correct attributes to be allowed inside the "body" element.');

            return true;
        }

        return false;
    }

    /**
     * Is this element allowed inside a body element?
     *
     * https://html.spec.whatwg.org/multipage/semantics.html#allowed-in-the-body
     *
     * @return bool True if allowed.
     */
    public function isAllowedInBody()
    {
        if ($this->hasAttribute('itemprop')) {
            return true;
        }

        if ($this->hasAttribute('rel') &&
            ($this->attributes['rel']->getValue() == 'pingback' ||
             $this->attributes['rel']->getValue() == 'prefetch' ||
             $this->attributes['rel']->getValue() == 'stylesheet')) {
            return true;
        }

        return false;
    }
}
