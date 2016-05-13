<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Tokens\ElementTypes\FlowContent;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "caption" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-caption-element
 */
class Caption extends OpenElement implements FlowContent
{
    /**
     * @todo Caption must be *first* child of table.
     */
    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        $parent = $this->getParent();
        if ($parent->getType() !== Token::ELEMENT &&
            $parent->getName() != 'table') {
            $logger->debug($this . ' must be child of "table" element.');

            return true;
        }

        return false;
    }
}
