<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

class Html extends OpenElement
{
    protected function getAllowedAttributes()
    {
        $htmlAllowedAttributes = array(
            '/^manifest$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $htmlAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger = null)
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
                    $this->removeChild($child);
                    if ($logger !== null) {
                        $logger->debug('Removing ' . $child . '. HTML element only allows HEAD and BODY child elements.');
                    }
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
                    $this->removeChild($child);
                    if ($logger !== null) {
                        $logger->debug('Removing ' . $child . '. Only one HEAD element allowed.');
                    }

                    continue;
                }
            } elseif ($child->getName() == 'body') {
                $bodyCount++;

                // Remove extraneous BODY elements.
                if ($bodyCount > 1 &&
                    $this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
                    $this->removeChild($child);
                    if ($logger !== null) {
                        $logger->debug('Removing ' . $child . '. Only one BODY element allowed.');
                    }

                    continue;
                }
            }
        }

        // Handle missing HEAD element child.
        if ($headCount == 0) {
            $head = new Head($this->configuration, 'head');
            $this->prependChild($head);
            if ($logger !== null) {
                $logger->debug('Missing HEAD element added.');
            }
        }

        // Handle missing BODY element child.
        if ($bodyCount == 0) {
            $body = new Body($this->configuration, 'body');
            $this->appendChild($body);
            if ($logger !== null) {
                $logger->debug('Missing BODY element added.');
            }
        }

        // Handle BODY before HEAD.
        if (!$headIsFirst && $bodyCount > 0) {
            foreach ($this->children as $key => $child) {
                if ($child->getType() == Token::ELEMENT && $child->getName() == 'body') {
                    unset($this->children[$key]);
                    $this->appendChild($child);
                    if ($logger !== null) {
                        $logger->debug('Moved BODY element to end of HTML children.');
                    }

                    break;
                }
            }
        }

        return true;
    }
}
