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
        $tokenizer = new HtmlTokenizer(false);
        $basicTokenCollection = $tokenizer->parse((string) $html);

        $tokenContainer = new TokenContainer($this->configuration);
        foreach ($basicTokenCollection as $basicToken) {
            $tokenContainer->appendChild($this->createToken($basicToken));
        }

        return $tokenContainer;
    }

    private function createToken(BasicToken $basicToken)
    {
        switch ($basicToken->getType()) {
        case 'cdata':
            return new CData(
                $this->configuration,
                $basicToken->getLine(),
                $basicToken->getPosition(),
                $basicToken->getValue()
            );

        case 'comment':
            return new Comment(
                $this->configuration,
                $basicToken->getLine(),
                $basicToken->getPosition(),
                $basicToken->getValue()
            );

        case 'doctype':
            return new DocType(
                $this->configuration,
                $basicToken->getLine(),
                $basicToken->getPosition(),
                $basicToken->getValue()
            );

        case 'php':
            return new Php(
                $this->configuration,
                $basicToken->getLine(),
                $basicToken->getPosition(),
                $basicToken->getValue()
            );

        case 'text':
            return new Text(
                $this->configuration,
                $basicToken->getLine(),
                $basicToken->getPosition(),
                $basicToken->getValue()
            );
        }

        return $this->createElement($basicToken);
    }

    private function createElement(BasicElement $basicElement)
    {
        // Primary class name.
        $elementClassName = 'Groundskeeper\\Tokens\\Elements\\' .
            ucfirst(strtolower($basicElement->getName()));
        if (!class_exists($elementClassName)) {
            // Secondary class name.
            // For elements whose names conflict with PHP keywords: var
            $elementClassName = 'Groundskeeper\\Tokens\\Elements\\' .
                ucfirst(strtolower($basicElement->getName())) . 'Element';
            if (!class_exists($elementClassName)) {
                $elementClassName = 'Groundskeeper\\Tokens\\Element';
            }
        }

        $cleanableElement = new $elementClassName(
            $this->configuration,
            $basicElement->getLine(),
            $basicElement->getPosition(),
            $basicElement->getName(),
            $basicElement->getAttributes()
        );

        foreach ($basicElement->getChildren() as $basicChild) {
            $cleanableElement->appendChild(
                $this->createToken($basicChild)
            );
        }

        return $cleanableElement;
    }
}
