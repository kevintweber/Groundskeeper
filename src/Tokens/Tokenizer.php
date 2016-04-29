<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\Token as CleanableToken;
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
        $tokenizer = new HtmlTokenizer(
            $this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_THROW
        );
        $basicTokenCollection = $tokenizer->parse((string) $html);

        $tokenContainer = new TokenContainer($this->configuration);
        foreach ($basicTokenCollection as $basicToken) {
            $tokenContainer->appendChild($this->createToken($basicToken));
        }

        return $tokenContainer;
    }

    private function createToken(BasicToken $basicToken, CleanableToken $parent = null)
    {
        switch ($basicToken->getType()) {
        case 'cdata':
            return new CData(
                $this->configuration,
                $parent,
                $basicToken->getValue()
            );

        case 'comment':
            return new Comment(
                $this->configuration,
                $parent,
                $basicToken->getValue()
            );

        case 'doctype':
            return new DocType(
                $this->configuration,
                $parent,
                $basicToken->getValue()
            );

        case 'element':
            return $this->createElement($basicToken, $parent);

        case 'text':
            return new Text(
                $this->configuration,
                $parent,
                $basicToken->getValue()
            );
        }

        throw new \RuntimeException(
            'Invalid token type: ' . $basicToken->getType()
        );
    }

    private function createElement(BasicElement $basicElement, CleanableToken $parent = null)
    {
        $elementClassName = 'Groundskeeper\\Tokens\\Elements\\' .
            ucfirst(strtolower($basicElement->getName()));
        if (!class_exists($elementClassName)) {
            $elementClassName = 'Groundskeeper\\Tokens\\Elements\\Element';
        }

        $cleanableElement = new $elementClassName(
            $this->configuration,
            $basicElement->getName(),
            $basicElement->getAttributes(),
            $parent
        );

        foreach ($basicElement->getChildren() as $basicChild) {
            $cleanableElement->appendChild(
                $this->createToken($basicChild, $cleanableElement)
            );
        }

        return $cleanableElement;
    }
}
