<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Exceptions\ValidationException;
use Groundskeeper\Tokens\Elements\Element;
use Psr\Log\LoggerInterface;

class TokenContainer implements Cleanable, ContainsChildren, Removable
{
    /** @var array[Token] */
    private $children;

    /** @var Configuration */
    private $configuration;

    /**
     * Constructor
     */
    public function __construct(Configuration $configuration)
    {
        $this->children = array();
        $this->configuration = $configuration;
    }

    /**
     * Required by ContainsChildren interface.
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Required by ContainsChildren interface.
     */
    public function hasChild(Token $token)
    {
        return array_search($token, $this->children) !== false;
    }

    /**
     * Required by ContainsChildren interface.
     */
    public function addChild(Token $token)
    {
        $this->children[] = $token;

        return $this;
    }

    /**
     * Required by ContainsChildren interface.
     */
    public function removeChild(Token $token)
    {
        $key = array_search($token, $this->children);
        if ($key !== false) {
            unset($this->children[$key]);

            return true;
        }

        return false;
    }

    /**
     * Required by Cleanable interface.
     */
    public function clean(LoggerInterface $logger = null)
    {
        if ($this->configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        foreach ($this->children as $child) {
            if ($child instanceof Cleanable) {
                $isClean = $child->clean($logger);
                if (!$isClean) {
                    /// @todo
                }
            }
        }

        return true;
    }

    /**
     * Required by the Removable interface.
     */
    public function remove(LoggerInterface $logger = null)
    {
        $hasRemovableTypes = $this->configuration->get('type-blacklist') !==
            Configuration::TYPE_BLACKLIST_NONE;
        $hasRemovableElements = $this->configuration->get('element-blacklist') !==
            Configuration::ELEMENT_BLACKLIST_NONE;
        foreach ($this->children as $key => $child) {
            // Check types.
            if ($hasRemovableTypes &&
                !$this->configuration->isAllowedType($child->getType())) {
                unset($this->children[$key]);
                if ($logger !== null) {
                    $logger->debug('Removing token of type: ' . $child->getType());
                }

                continue;
            }

            // Check elements.
            if ($hasRemovableElements &&
                $child instanceof Element &&
                !$this->configuration->isAllowedElement($child->getName())) {
                unset($this->children[$key]);
                if ($logger !== null) {
                    $logger->debug('Removing element of type: ' . $child->getName());
                }

                continue;
            }

            // Check children.
            if ($child instanceof Removable) {
                $child->remove($logger);
            }
        }
    }
}
