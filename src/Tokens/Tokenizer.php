<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Kevintweber\HtmlTokenizer\HtmlTokenizer;
use Kevintweber\HtmlTokenizer\Tokens\Element as BasicElement;
use Kevintweber\HtmlTokenizer\Tokens\Token as BasicToken;

class Tokenizer
{
    /** @var Configuration */
    private $configuration;

    /**
     * Constructor
     */
    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function tokenize($html)
    {
        if (!is_string($html)) {
            throw new \InvalidArgumentException('Html must be a string.');
        }

        $tokenizer = new HtmlTokenizer(
            $this->configuration->get('error-strategy') == 'throw'
        );
        $basicTokenCollection = $tokenizer->parse($html);

        $cleanableTokens = array();
        foreach ($basicTokenCollection as $basicToken) {
            $cleanableTokens[] = $this->createToken($basicToken);
        }

        return $cleanableTokens;
    }

    private function createToken(BasicToken $basicToken)
    {
        switch ($basicToken->getType()) {
        case 'cdata':
            return new CData(
                $basicToken->getParent(),
                $basicToken->getValue()
            );

        case 'comment':
            return new Comment(
                $basicToken->getParent(),
                $basicToken->getValue()
            );

        case 'doctype':
            return new DocType(
                $basicToken->getParent(),
                $basicToken->getValue()
            );

        case 'element':
            return static::createElement($basicToken);

        case 'text':
            return new Text(
                $basicToken->getParent(),
                $basicToken->getValue()
            );
        }

        throw new \RuntimeException(
            'Invalid token type: ' . $basicToken->getType()
        );
    }

    private function createElement(BasicElement $basicElement)
    {
        $elementClassName = 'Groundskeeper\\Tokens\\Elements\\' .
            ucfirst(strtolower($basicElement->getName()));
        if (!class_exists($elementClassName)) {
            $elementClassName = 'Groundskeeper\\Tokens\\Elements\\Element';
        }

        $cleanableElement = new $elementClassName(
            $basicElement->getName(),
            $basicElement->getAttributes(),
            $basicElement->getParent()
        );

        foreach ($basicElement->getChildren() as $basicChild) {
            $cleanableElement->addChild(
                $this->createToken($basicChild)
            );
        }

        return $cleanableElement;
    }
}
