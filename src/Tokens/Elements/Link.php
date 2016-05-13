<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\ClosedElement;
use Groundskeeper\Tokens\ElementTypes\MetadataContent;
use Psr\Log\LoggerInterface;

class Link extends ClosedElement implements MetadataContent
{
    protected function getAllowedAttributes()
    {
        $linkAllowedAttributes = array(
            '/^href$/i' => Element::ATTR_URI,
            '/^crossorigin$/i' => Element::ATTR_CS_STRING,
            '/^rel$/i' => Element::ATTR_CI_SSENUM . '("alternate","author","help","icon","license","next","pingback","prefetch","prev","search","stylesheet")',
            '/^media$/i' => Element::ATTR_CI_STRING,
            '/^hreflang$/i' => Element::ATTR_CS_STRING,
            '/^type$/i' => Element::ATTR_CI_STRING,
            '/^sizes$/i' => Element::ATTR_CI_STRING
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
            $logger->debug($this . ' requires "href" attribute.');

            return true;
        }

        // Must have either "rel" or "itemprop" attribute, but not both.
        $attrCount = 0;
        foreach ($this->attributes as $key => $value) {
            if ($key == 'rel' || $key == 'itemprop') {
                ++$attrCount;
            }

            if ($attrCount > 1) {
                // If both, then we don't know which one should be kept,
                // so we recommend to delete the entire element.
                $logger->debug($this . ' requires either "rel" or "itemprop" attribute, but not both.');

                return true;
            }
        }

        if ($attrCount == 0) {
            $logger->debug($this . ' requires either "rel" or "itemprop" attribute.');

            return true;
        }

        // If inside "body" element, then we check if allowed.
        $body = new Body($this->configuration, 'body');
        if ($this->hasAncestor($body) && !$this->isAllowedInBody()) {
            $logger->debug($this . ' does not have the correct attributes to be allowed inside the "body" element.');

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
            ($this->attributes['rel'] == 'pingback' ||
             $this->attributes['rel'] == 'prefetch' ||
             $this->attributes['rel'] == 'stylesheet')) {
            return true;
        }

        return false;
    }
}
