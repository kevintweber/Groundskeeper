<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;

class Html extends OpenElement
{
    protected function getAllowedAttrbutes()
    {
        $htmlAllowedAttributes = array(
            '/^manifest$/i' => Element::ATTR_CS_STRING
        );

        return array_merge(
            $htmlAllowedAttributes,
            parent::getAllowedAttrbutes()
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

        if ($this->getParent() !== null) {
            return $this->handleValidationError('Html element must not be nested.');
        }
    }
}
