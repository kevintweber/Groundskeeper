<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\NonParticipating;
use Psr\Log\LoggerInterface;

/**
 * "html" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-html-element
 */
class Html extends OpenElement
{
    protected function getAllowedAttributes()
    {
        $htmlAllowedAttributes = array(
            '/^manifest$/i' => Attribute::URI
        );

        return array_merge(
            $htmlAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function fixSelf(LoggerInterface $logger)
    {
        $bodyCount = 0;
        $headCount = 0;
        $headIsFirst = false;
        foreach ($this->children as $key => $child) {
            // Check for HEAD and BODY
            if ($child instanceof Head) {
                ++$headCount;
                if ($bodyCount == 0) {
                    $headIsFirst = true;
                }
            } elseif ($child instanceof Body) {
                ++$bodyCount;
            }
        }

        // Handle missing HEAD element child.
        if ($headCount == 0) {
            $logger->debug('Missing "head" element added.');
            $head = new Head($this->configuration, 'head');
            $this->prependChild($head);
        }

        // Handle missing BODY element child.
        if ($bodyCount == 0) {
            $logger->debug('Missing "body" element added.');
            $body = new Body($this->configuration, 'body');
            $this->appendChild($body);
        }

        // Handle BODY before HEAD.
        if (!$headIsFirst && $bodyCount > 0 && $headCount > 0) {
            foreach ($this->children as $key => $child) {
                if ($child instanceof Body) {
                    $logger->debug('Moved "body" element to end of "html" element children.');
                    unset($this->children[$key]);
                    $this->appendChild($child);

                    break;
                }
            }
        }
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
        $bodyCount = 0;
        $headCount = 0;
        foreach ($this->children as $key => $child) {
            if ($child instanceof NonParticipating) {
                continue;
            }

            // Check for HEAD and BODY
            if ($child instanceof Head) {
                ++$headCount;

                // Remove extraneous HEAD elements.
                if ($headCount > 1) {
                    $logger->debug('Removing ' . $child . '. Only one "head" element allowed.');
                    $this->removeChild($child);

                    continue;
                }
            } elseif ($child instanceof Body) {
                ++$bodyCount;

                // Remove extraneous BODY elements.
                if ($bodyCount > 1) {
                    $logger->debug('Removing ' . $child . '. Only one "body" element allowed.');
                    $this->removeChild($child);

                    continue;
                }
            } else {
                $logger->debug('Removing ' . $child . '. Only "head" or "body" elements are allowed as "html" element children.');
                $this->removeChild($child);
            }
        }
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // HTML element must not have parent elements.
        if ($this->getParent() !== null) {
            $logger->debug('Removing ' . $this . '. Must not have a parent element.');

            return true;
        }

        return false;
    }
}
