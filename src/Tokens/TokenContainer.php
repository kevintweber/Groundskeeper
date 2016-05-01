<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Exceptions\ValidationException;
use Groundskeeper\Tokens\Elements\Element;
use Psr\Log\LoggerInterface;

final class TokenContainer implements Cleanable, ContainsChildren, Removable
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
        return array_search($token, $this->children, true) !== false;
    }

    /**
     * Required by ContainsChildren interface.
     */
    public function appendChild(Token $token)
    {
        $this->children[] = $token;

        return $this;
    }

    /**
     * Required by ContainsChildren interface.
     */
    public function prependChild(Token $token)
    {
        array_unshift($this->children, $token);

        return $this;
    }

    /**
     * Required by ContainsChildren interface.
     */
    public function removeChild(Token $token)
    {
        $key = array_search($token, $this->children, true);
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
        return AbstractToken::cleanChildTokens(
            $this->configuration,
            $this->children,
            $logger
        );
    }

    /**
     * Required by the Removable interface.
     */
    public function remove(LoggerInterface $logger = null)
    {
        $hasRemovableElements = $this->configuration->get('element-blacklist') != '';
        $hasRemovableTypes = $this->configuration->get('type-blacklist') != '';
        foreach ($this->children as $child) {
            // Check types.
            if ($hasRemovableTypes &&
                !$this->configuration->isAllowedType($child->getType())) {
                if ($logger !== null) {
                    $logger->debug('Removing ' . $child);
                }

                $this->removeChild($child);
                continue;
            }

            // Check elements.
            if ($hasRemovableElements &&
                $child->getType() == Token::ELEMENT &&
                !$this->configuration->isAllowedElement($child->getName())) {
                if ($logger !== null) {
                    $logger->debug('Removing ' . $child);
                }

                $this->removeChild($child);
                continue;
            }

            // Check children.
            if ($child instanceof Removable) {
                $child->remove($logger);
            }
        }
    }
}
