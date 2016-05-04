<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "html" element
 */
class Html extends OpenElement
{
    protected function getAllowedAttributes()
    {
        $htmlAllowedAttributes = array(
            '/^manifest$/i' => Element::ATTR_URI
        );

        return array_merge(
            $htmlAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger)
    {
        // HTML element must not have parent elements.
        if ($this->getParent() !== null) {
            return false;
        }

        // Only chidlren allowed are HEAD element followed by BODY element.
        $bodyCount = 0;
        $headCount = 0;
        $headIsFirst = false;
        foreach ($this->children as $key => $child) {
            // Ignore comments.
            if ($child->getType() == Token::COMMENT) {
                continue;
            }

            // Invalid token.
            if ($child->getType() != Token::ELEMENT) {
                if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                    $logger->debug('Removing ' . $child . '. "html" element only allows "head" and "body" elements children.');
                    $this->removeChild($child);
                }

                continue;
            }

            // Check for HEAD and BODY
            if ($child->getName() == 'head') {
                $headCount++;
                if ($bodyCount == 0) {
                    $headIsFirst = true;
                }

                // Remove extraneous HEAD elements.
                if ($headCount > 1 &&
                    $this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                    $logger->debug('Removing ' . $child . '. Only one "head" element allowed.');
                    $this->removeChild($child);

                    continue;
                }
            } elseif ($child->getName() == 'body') {
                $bodyCount++;

                // Remove extraneous BODY elements.
                if ($bodyCount > 1 &&
                    $this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                    $logger->debug('Removing ' . $child . '. Only one BODY element allowed.');
                    $this->removeChild($child);

                    continue;
                }
            } elseif ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                $logger->debug('Removing ' . $child . '. Only "head" or "body" elements are allowed as "html" element children.');
                $this->removeChild($child);

                continue;
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
                if ($child->getType() == Token::ELEMENT && $child->getName() == 'body') {
                    $logger->debug('Moved "body" element to end of "html" element children.');
                    unset($this->children[$key]);
                    $this->appendChild($child);

                    break;
                }
            }
        }

        return true;
    }
}
