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

    /**
     * Required by the Cleanable interface.
     */
    public function clean(LoggerInterface $logger = null)
    {
        if ($this->configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        parent::clean($logger);

        // "base" must be child of "head".
        if ($this->getParent() === null || $this->getParent()->getName() !== 'head') {
            return false;
        }

        // Must have either "href" or "target" attribute or both.
        if (!$this->hasAttribute('href') && !$this->hasAttribute('target')) {
            return false;
        }

        return true;
    }
}
