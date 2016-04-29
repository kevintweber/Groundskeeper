<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Exceptions\ValidationException;
use Groundskeeper\Tokens\Elements\Elements;
use Psr\Log\LoggerInterface;

abstract class AbstractToken implements Token
{
    /** @var Configuration */
    protected $configuration;

    /** @var int */
    private $depth;

    /** @var null|Token */
    private $parent;

    /** @var string */
    private $type;

    /**
     * Constructor
     */
    public function __construct($type, Configuration $configuration, Token $parent = null)
    {
        if (!$this->isValidType($type)) {
            throw new \InvalidArgumentException('Invalid type: ' . $type);
        }

        $this->configuration = $configuration;
        $this->setParent($parent);
        $this->type = $type;
    }

    /**
     * Required by the Token interface.
     */
    public function getDepth()
    {
        return $this->depth;
    }

    /**
     * Required by the Token interface.
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Chainable setter for 'parent'.
     */
    public function setParent(Token $parent = null)
    {
        $this->depth = 0;
        if ($parent instanceof Token) {
            $this->depth = $parent->getDepth() + 1;
        }

        $this->parent = $parent;

        return $this;
    }

    /**
     * Required by the Token interface.
     */
    public function getType()
    {
        return $this->type;
    }

    protected function isValidType($type)
    {
        return $type === Token::CDATA
            || $type === Token::COMMENT
            || $type === Token::DOCTYPE
            || $type === Token::ELEMENT
            || $type === Token::TEXT;
    }

    public static function cleanChildTokens(Configuration $configuration, array &$children, LoggerInterface $logger = null)
    {
        if ($configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        foreach ($children as $key => $child) {
            if ($child instanceof Cleanable) {
                $isClean = $child->clean($logger);
                if (!$isClean) {
                    $message = 'invalid token. Unable to fix: ' . $child->getType();
                    if ($child instanceof Element) {
                        $message = 'invalid element. Unable to fix: ' . $child->getName();
                    }

                    if ($configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_THROW) {
                        throw new ValidationException(ucfirst($message));
                    }

                    if ($configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_FIX || $configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_REMOVE) {
                        unset($children[$key]);
                        if ($logger !== null) {
                            $logger->debug('Removing ' . $message);
                        }
                    }
                }
            }
        }

        return true;
    }
}
