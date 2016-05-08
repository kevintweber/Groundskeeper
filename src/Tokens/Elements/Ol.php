<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "ol" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-ol-element
 */
class Ol extends OpenElement implements FlowContent
{
    protected function getAllowedAttributes()
    {
        $olAllowedAttributes = array(
            '/^reversed$/i' => Element::ATTR_BOOL,
            '/^start$/i' => Element::ATTR_INT,
            '/^type$/i' => Element::ATTR_CS_STRING,
        );

        return array_merge(
            $olAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            // Only "li" and ScriptSupporting elements allowed.
            foreach ($this->children as $child) {
                if ($child->getType() == Token::COMMENT) {
                    continue;
                }

                if ($child->getType() != Token::ELEMENT) {
                    $logger->debug('Removing ' . $child . '. Only elements "li" and script supporting elements allowed as children of "ol" element.');
                    $this->removeChild($child);

                    continue;
                }

                if ($child->getName() == 'li' || $child instanceof ScriptSupporting) {
                    continue;
                }

                $logger->debug('Removing ' . $child . '. Only elements "li" and script supporting elements allowed as children of "ol" element.');
                $this->removeChild($child);
            }
        }

        return true;
    }
}
