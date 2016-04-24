<?php

namespace Groundskeeper\Tokens\Elements;

class Link extends ClosedElement
{
    protected function getAllowedAttributes()
    {
        $linkAllowedAttributes = array(
            '/^href$/i' => Element::ATTR_URI,
            '/^crossorigin$/i' => Element::ATTR_CS_STRING,
            '/^rel$/i' => Element::ATTR_CS_STRING,
            '/^media$/i' => Element::ATTR_CS_STRING,
            '/^hreflang$/i' => Element::ATTR_CS_STRING,
            '/^type$/i' => Element::ATTR_CS_STRING,
            '/^sizes$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $linkAllowedAttributes,
            parent::getAllowedAttributes()
        );
    }

    public function validate(Configuration $configuration)
    {
        parent::validate($configuration);

        // If not valid, then we are done.
        if (!$this->isValid) {
            return;
        }

        // If no cleaning, then we are done.
        if ($configuration->get('clean-strategy') == 'none') {
            return;
        }

        // Must have "href" attribute.
        if (!$this->hasAttribute('href')) {
            $this->handleValidationError(
                $configuration,
                'Link element must have "href" attribute.'
            );
        }

        // Must have either "rel" or "itemprop" attribute, but not both.
        /// @todo
    }
}
