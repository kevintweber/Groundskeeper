<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\EmbeddedContent;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InteractiveContent;
use Groundskeeper\Tokens\ElementTypes\MediaElement;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\TransparentElement;
use Groundskeeper\Tokens\Text;
use Psr\Log\LoggerInterface;

/**
 * "audio" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-audio-element
 */
class Audio extends OpenElement implements FlowContent, PhrasingContent, EmbeddedContent, InteractiveContent, TransparentElement, MediaElement
{
    protected function getAllowedAttributes()
    {
        $audioAllowedAttributes = array(
            '/^src$/i' => Attribute::URI,
            '/^crossorigin$/i' => Attribute::CS_STRING,
            '/^preload$/i' => Attribute::CI_ENUM . '("","none","metadata","auto"|"")',
            '/^autoplay$/i' => Attribute::BOOL,
            '/^mediagroup$/i' => Attribute::CS_STRING,
            '/^loop$/i' => Attribute::BOOL,
            '/^muted$/i' => Attribute::BOOL,
            '/^controls$/i' => Attribute::BOOL
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
            if ($child instanceof NonParticipanting ||
                $child instanceof Text) {
                continue;
            }

            if (!$hasSrc && $child instanceof Source) {
                continue;
            }

            if ($child instanceof Track) {
                continue;
            }

            if ($child instanceof TransparentElement &&
                $child->isTransparentElement()) {
                continue;
            }

            $logger->debug('Removing ' . $child . '. Only text, "source", "track", and transparent elements allowed as children of "audio" element.');
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
