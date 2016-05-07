<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\InlineElement;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "time" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-time-element
 */
class Time extends OpenElement implements FlowContent, PhrasingContent
{
    protected function getAllowedAttributes()
    {
        $timeAllowedAttributes = array(
            '/^datetime$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $timeAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function doClean(LoggerInterface $logger)
    {
        // If attribute "datetime" is not present, then only TEXT type
        // children allowed.
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT && !$this->hasAttribute('datetime')) {
            foreach ($this->children as $child) {
                if ($child->getType() == Token::COMMENT) {
                    continue;
                }

                if ($child->getType() != Token::TEXT) {
                    $logger->debug('Removing ' . $child . '. Element "time" without "datetime" attribute may only contain TEXT.');
                    $this->removeChild($child);

                    continue;
                }
            }
        }

        return true;
    }
}
