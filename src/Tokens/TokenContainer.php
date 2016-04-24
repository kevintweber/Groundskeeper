<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Psr\Log\LoggerInterface;

class TokenContainer implements Cleanable, ContainsChildren
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
            return;
        }

        foreach ($this->children as $child) {
            if ($child instanceof Cleanable) {
                $child->clean($logger);
            }
        }
    }
}
