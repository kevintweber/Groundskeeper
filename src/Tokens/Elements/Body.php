<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningRoot;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

/**
 * "body" element
 *
 * https://html.spec.whatwg.org/multipage/semantics.html#the-body-element
 */
class Body extends OpenElement implements SectioningRoot
{
    protected function getAllowedAttributes()
    {
        $bodyAllowedAttributes = array(
            '/^onafterprint$/i' => Element::ATTR_JS,
            '/^onbeforeprint$/i' => Element::ATTR_JS,
            '/^onbeforeunload$/i' => Element::ATTR_JS,
            '/^onhashchange$/i' => Element::ATTR_JS,
            '/^onlanguagechange$/i' => Element::ATTR_JS,
            '/^onmessage$/i' => Element::ATTR_JS,
            '/^onoffline$/i' => Element::ATTR_JS,
            '/^ononline$/i' => Element::ATTR_JS,
            '/^onpagehide$/i' => Element::ATTR_JS,
            '/^onpageshow$/i' => Element::ATTR_JS,
            '/^onpopstate$/i' => Element::ATTR_JS,
            '/^onrejectionhandled$/i' => Element::ATTR_JS,
            '/^onstorage$/i' => Element::ATTR_JS,
            '/^onunhandledrejection$/i' => Element::ATTR_JS,
            '/^onunload$/i' => Element::ATTR_JS
        );

        return array_merge(
            $bodyAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        // "body" element must be a child of "html" element.
        if ($this->getParent() !== null &&
            !($this->getParent() instanceof Html)) {
            $logger->debug('Removing ' . $this . '. Must be a child of "html" element.');

            return true;
        }

        return false;
    }
}
