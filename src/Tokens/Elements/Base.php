<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;

class Base extends ClosedElement
{
    protected function getAllowedAttributes()
    {
        $baseAllowedAttributes = array(
            '/^href$/i' => Element::ATTR_URI,
            '/^target$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $baseAllowedAttributes,
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

        // "base" must be child of "head".
        if ($this->getParent() === null || $this->getParent()->getName() !== 'head') {
            $this->handleValidationError(
                $configuration,
                'Base element must be a child of a "head" element.'
            );
        }

        // Must have either "href" or "target" attribute or both.
        if (!$this->hasAttribute('href') && !$this->hasAttribute('target')) {
            $this->handleValidationError(
                $configuration,
                'Base element must have either "href" or "target" attribute or both.'
            );
        }
    }
}
