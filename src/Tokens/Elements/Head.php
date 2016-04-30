<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

class Head extends OpenElement
{
    /**
     * Required by the Cleanable interface.
     */
    public function clean(LoggerInterface $logger = null)
    {
        if ($this->configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        parent::clean($logger);

        // HEAD must contain only metadata content elements.
        // HEAD must contain exactly one TITLE element.
        // HEAD must contain either 0 or 1 BASE element.
        $titleCount = 0;
        $baseCount = 0;
        foreach ($this->children as $key => $child) {
            if (!$child instanceof MetadataContent && $child->getType() !== 'comment') {
                if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                    return false;
                }

                $this->removeChildTokenHelper(
                    $key,
                    'Removing invalid token (' . $child->getType() . '). Only children of metadata content allowed.',
                    $logger
                );
            }

            if ($child->getType() != Token::ELEMENT) {
                continue;
            }

            if ($child->getName() == 'title') {
                $titleCount++;
                if ($titleCount > 1) {
                    if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                        return false;
                    }

                    $this->removeChildTokenHelper(
                        $key,
                        'Removing invalid TITLE element. Only one TITLE element allowed.',
                        $logger
                    );
                }
            } elseif ($child->getName() == 'base') {
                $baseCount++;
                if ($baseCount > 1) {
                    if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                        return false;
                    }

                    $this->removeChildTokenHelper(
                        $key,
                        'Removing invalid BASE element. Maximum one BASE element allowed.',
                        $logger
                    );
                }
            }
        }

        // Missing title.
        if ($titleCount == 0) {
            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_THROW) {
                throw new ValidationException('HEAD element is missing a TITLE element child.');
            }

            if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                return false;
            }

            $title = new Title($this->configuration, 'title');
            $this->prependChild($title);
        }

        return true;
    }
}
