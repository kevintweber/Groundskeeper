<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\EmbeddedContent;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\TransparentElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "audio" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-audio-element
 */
class Audio extends OpenElement implements FlowContent, PhrasingContent, EmbeddedContent, InteractiveContent, TransparentElement
{
    protected function getAllowedAttributes()
    {
        $audioAllowedAttributes = array(
            '/^src$/i' => Element::ATTR_URI,
            '/^crossorigin$/i' => Element::ATTR_CS_STRING,
            '/^preload$/i' => Element::ATTR_CI_ENUM . '("","none","metadata","auto"|"")',
            '/^autoplay$/i' => Element::ATTR_BOOL,
            '/^mediagroup$/i' => Element::ATTR_CS_STRING,
            '/^loop$/i' => Element::ATTR_BOOL,
            '/^muted$/i' => Element::ATTR_BOOL,
            '/^controls$/i' => Element::ATTR_BOOL
        );

        return array_merge(
            $audioAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        $hasSrc = $this->hasAttribute('src');
        foreach ($this->children as $child) {
            if ($child->getType() == Token::COMMENT) {
                continue;
            }

            if ($child->getType() == Token::TEXT) {
                continue;
            }

            if ($child->getType() !== Token::ELEMENT) {
                $logger->debug('Removing ' . $child . '. Only elements allowed as children of "audio" element.');
                $this->removeChild($child);

                continue;
            }

            if (!$hasSrc && $child->getName() == 'source') {
                continue;
            }

            if ($child->getName() == 'track') {
                continue;
            }

            if ($child instanceof TransparentElement &&
                $child->isTransparentElement()) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only "source", "track", and transparent elements allowed as children of "audio" element.');
            $this->removeChild($child);
        }
    }

    public function isInteractiveContent()
    {
        return $this->hasAttribute('controls');
    }

    public function isTransparentElement()
    {
        /// @todo Implement this.  Complicated checks involved.
        return true;
    }
}
