<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Tokens\Attribute;
use Groundskeeper\Tokens\Element;
use Groundskeeper\Tokens\ElementTypes\OpenElement;
use Groundskeeper\Tokens\ElementTypes\SectioningRoot;
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
            '/^onafterprint$/i' => Attribute::JS,
            '/^onbeforeprint$/i' => Attribute::JS,
            '/^onbeforeunload$/i' => Attribute::JS,
            '/^onhashchange$/i' => Attribute::JS,
            '/^onlanguagechange$/i' => Attribute::JS,
            '/^onmessage$/i' => Attribute::JS,
            '/^onoffline$/i' => Attribute::JS,
            '/^ononline$/i' => Attribute::JS,
            '/^onpagehide$/i' => Attribute::JS,
            '/^onpageshow$/i' => Attribute::JS,
            '/^onpopstate$/i' => Attribute::JS,
            '/^onrejectionhandled$/i' => Attribute::JS,
            '/^onstorage$/i' => Attribute::JS,
            '/^onunhandledrejection$/i' => Attribute::JS,
            '/^onunload$/i' => Attribute::JS
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
