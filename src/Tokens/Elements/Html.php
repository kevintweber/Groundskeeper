<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
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

    /**
     * Required by the Cleanable interface.
     */
    public function clean(LoggerInterface $logger = null)
    {
        if ($this->configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        parent::clean($logger);

        // HTML element must not have parent elements.
        if ($this->getParent() !== null) {
            return false;
        }

        // HEAD element followed by BODY element.
        $bodyCount = 0;
        $headCount = 0;
        $headIsFirst = false;
        foreach ($this->children as $key => $child) {
            // Ignore comments.
            if ($child->getType() == 'comment') {
                continue;
            }

            // Check for HEAD and BODY
            if ($child->getType() == 'element') {
                if ($child->getName() == 'head') {
                    $headCount++;
                    if ($bodyCount == 0) {
                        $headIsFirst = true;
                    }
                } elseif ($child->getName() == 'body') {
                    $bodyCount++;
                }
            } else {
                // Invalid token.
                if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                    return false;
                }

                $this->removeChildTokenHelper(
                    $key,
                    'Invalid token (' . $child->getType() . ') should not be child of HTML element.',
                    $logger
                );
            }
        }

        // Handle missing HEAD element child.
        if ($headCount == 0) {
            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_THROW) {
                throw new ValidationException('HTML element is missing HEAD element as first child.');
            }

            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                return false;
            }

            $head = new Head($this->configuration, 'head');
            $this->prependChild($head);
            if ($logger !== null) {
                $logger->debug('Missing HEAD element added.');
            }
        }

        // Handle missing BODY element child.
        if ($bodyCount == 0) {
            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_THROW) {
                throw new ValidationException('HTML element is missing BODY element.');
            }

            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                return false;
            }

            $body = new Body($this->configuration, 'body');
            $this->appendChild($body);
            if ($logger !== null) {
                $logger->debug('Missing BODY element added.');
            }
        }

        // Handle multiple HEAD elements.
        if ($headCount > 1) {
            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_THROW) {
                throw new ValidationException('HTML element can only have 1 HEAD element child.');
            }

            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                return false;
            }

            // Remove extraneous HEAD elements.
            $keepHead = true;
            foreach ($this->children as $key => $child) {
                if ($child->getType() == 'element' && $child->getName() == 'head') {
                    if ($keepHead) {
                        $keepHead = false;
                    } else {
                        unset($this->children[$key]);
                        if ($logger !== null) {
                            $logger->debug('Removed extraneous HEAD element.');
                        }
                    }
                }
            }
        }

        // Handle multiple BODY elements.
        if ($bodyCount > 1) {
            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_THROW) {
                throw new ValidationException('HTML element can only have 1 BODY element child.');
            }

            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                return false;
            }

            // Remove extraneous BODY elements.
            $keepBody = true;
            foreach ($this->children as $key => $child) {
                if ($child->getType() == 'element' && $child->getName() == 'body') {
                    if ($keepBody) {
                        $keepBody = false;
                    } else {
                        unset($this->children[$key]);
                        if ($logger !== null) {
                            $logger->debug('Removed extraneous BODY element.');
                        }
                    }
                }
            }
        }

        // Handle BODY before HEAD.
        if (!$headIsFirst && $bodyCount > 0) {
            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_THROW) {
                throw new ValidationException('HTML element requires the HEAD element to preceed the BODY element.');
            }

            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                return false;
            }

            foreach ($this->children as $key => $child) {
                if ($child->getType() == 'element' && $child->getName() == 'body') {
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
