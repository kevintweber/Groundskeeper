<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\EmbeddedContent;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\PhrasingContent;
use Groundskeeper\Tokens\ElementTypes\ScriptSupporting;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "picture" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-picture-element
 */
class Picture extends OpenElement implements FlowContent, PhrasingContent, EmbeddedContent
{
    protected function doClean(LoggerInterface $logger)
    {
        // Children can consist of zero or more "source" elements,
        // followed by one "img" element, optionally intermixed
        // with script-supporting elements.
        if ($this->configuration->get('clean-strategy') != Configuration::CLEAN_STRATEGY_LENIENT) {
            $encounteredImgElement = false;
            foreach ($this->children as $child) {
                if ($child->getType() == Token::COMMENT) {
                    continue;
                }

                if ($child->getType() !== Token::ELEMENT) {
                    $logger->debug('Removing ' . $child);
                    $this->removeChild($child);

                    continue;
                }

                if ($child instanceof ScriptSupporting) {
                    continue;
                }

                if ($child->getName() == 'img') {
                    if ($encounteredImgElement) {
                        $logger->debug('Removing ' . $child . '. Only one "img" element allowed as child of "picture" element.');
                        $this->removeChild($child);

                        continue;
                    }

                    $encounteredImgElement = true;

                    continue;
                }

                if ($child->getName() != 'source') {
                    $logger->debug('Removing ' . $child . '. Only one "img" element and zero to many "source" elements allowed as children of the "picture" element.');
                    $this->removeChild($child);

                    continue;
                }
            }
        }

        return true;
    }
}
