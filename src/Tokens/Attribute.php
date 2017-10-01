<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Psr\Log\LoggerInterface;

class Attribute
{
    const BOOL = 'ci_boo'; // boolean
    const CI_ENUM = 'ci_enu'; // case-insensitive enumeration
    const CI_SSENUM = 'ci_sse'; // case-insensitive space-separated enumeration
    const INT = 'ci_int'; // integer
    const JS = 'cs_jsc'; // javascript
    const CI_STRING = 'ci_str'; // case-insensitive string
    const CS_STRING = 'cs_str'; // case-sensitive string
    const URI = 'cs_uri'; // uri
    const UNKNOWN = 'cs_unk'; // unknown

    /** @var string */
    private $name;

    /** @var string */
    private $type;

    /** @var null|mixed */
    private $value;

    /** @var bool */
    private $isStandard;

    /**
     * Constructor
     */
    public function __construct(string $name, $value = null)
    {
        $this->name = $name;
        $this->type = self::UNKNOWN;
        $this->value = $value;
        $this->isStandard = false;
    }

    /**
     * Getter for 'name'.
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Getter for 'type'.
     */
    public function getType() : string
    {
        return $this->type;
    }

    /**
     * Chainable setter for 'type'.
     */
    public function setType(string $type)
    {
        $typeEnum = mb_substr($type, 0, 6);
        if ($typeEnum !== self::BOOL &&
            $typeEnum !== self::CI_ENUM &&
            $typeEnum !== self::CI_SSENUM &&
            $typeEnum !== self::INT &&
            $typeEnum !== self::JS &&
            $typeEnum !== self::CI_STRING &&
            $typeEnum !== self::CS_STRING &&
            $typeEnum !== self::URI &&
            $typeEnum !== self::UNKNOWN) {
            throw new \InvalidArgumentException('Invalid attribute type: ' . $typeEnum);
        }

        $this->type = $type;
    }

    /**
     * Getter for 'value'.
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * Getter for 'isStandard'.
     */
    public function getIsStandard() : bool
    {
        return $this->isStandard;
    }

    /**
     * Chainable setter for 'isStandard'.
     */
    public function setIsStandard(bool $isStandard)
    {
        $this->isStandard = $isStandard;
    }

    public function clean(Configuration $configuration, Element $element, LoggerInterface $logger)
    {
        if ($configuration->get('clean-strategy') === Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        // Remove non-standard attributes.
        if ($configuration->get(
                'clean-strategy'
            ) !== Configuration::CLEAN_STRATEGY_LENIENT && $this->isStandard === false) {
            $logger->debug('Removing non-standard attribute "' . $this->name . '" from ' . $element);

            return false;
        }

        // Validate attribute value.
        list($caseSensitivity, $attributeType) = explode('_', $this->type);

        // Standard is case-insensitive attribute values should be lower case.
        if ($caseSensitivity === 'ci' && $this->value !== true) {
            $newValue = strtolower($this->value);
            if ($newValue !== $this->value) {
                $logger->debug(
                    'Within ' . $element . ', the value for the attribute "' . $this->name . '" is case-insensitive.  The value has been converted to lower case.'
                );
                $this->value = $newValue;
            }
        }

        // Validate value types.
        switch ($attributeType) {
        case 'boo': // boolean
            $cleanResult = $this->cleanAttributeBoolean(
                $configuration,
                $element,
                $logger
            );
            if ($configuration->get('clean-strategy') !== Configuration::CLEAN_STRATEGY_LENIENT) {
                return $cleanResult;
            }

            break;

        case 'int': // integer
            $cleanResult = $this->cleanAttributeInteger(
                $configuration,
                $element,
                $logger
            );
            if ($configuration->get('clean-strategy') !== Configuration::CLEAN_STRATEGY_LENIENT) {
                return $cleanResult;
            }

            break;

        case 'str': // string
            $cleanResult = $this->cleanAttributeString(
                $configuration,
                $element,
                $logger
            );
            if ($configuration->get('clean-strategy') !== Configuration::CLEAN_STRATEGY_LENIENT) {
                return $cleanResult;
            }

            break;

        case 'uri': // URI
            $cleanResult = $this->cleanAttributeUri(
                $configuration,
                $element,
                $logger
            );
            if ($configuration->get('clean-strategy') !== Configuration::CLEAN_STRATEGY_LENIENT) {
                return $cleanResult;
            }

            break;
        }

        return true;
    }

    private function cleanAttributeBoolean(Configuration $configuration, Element $element, LoggerInterface $logger)
    {
        if ($this->value !== true) {
            $logger->debug(
                'Within ' . $element . ', the attribute "' . $this->name .
                '" is a boolean attribute.  The value has been removed.'
            );
            $this->value = true;
        }

        return true;
    }

    private function cleanAttributeInteger(Configuration $configuration, Element $element, LoggerInterface $logger)
    {
        if ($this->value === true || $this->value == '') {
            if ($configuration->get('clean-strategy') !== Configuration::CLEAN_STRATEGY_LENIENT) {
                $logger->debug(
                    'Within ' . $element . ', the value for the attribute "' . $this->name . '" is required to be an positive, non-zero integer.  The value is invalid, therefore the attribute has been removed.'
                );
            }

            return false;
        }

        if (!is_int($this->value)) {
            $originalValue = (string) $this->value;
            $this->value = (int) $this->value;
            if ($originalValue !== ((string) $this->value)) {
                $logger->debug(
                    'Within ' . $element . ', the value for the attribute "' . $this->name . '" is required to be an positive, non-zero integer.  The value has been converted to an integer.'
                );
            }
        }

        if ($this->value <= 0 && $configuration->get('clean-strategy') !== Configuration::CLEAN_STRATEGY_LENIENT) {
            $logger->debug(
                'Within ' . $element . ', the value for the attribute "' . $this->value . '" is required to be an positive, non-zero integer.  The value is invalid, therefore the attribute has been removed.'
            );

            return false;
        }

        return true;
    }

    private function cleanAttributeString(Configuration $configuration, Element $element, LoggerInterface $logger)
    {
        if ($this->value === true) {
            $logger->debug(
                'Within ' . $element . ', the attribute "' . $this->name . '" requires a string value.  The value is missing, therefore the attribute value is set to the attribute name.'
            );

            $this->value = $this->name;
        }

        return true;
    }

    private function cleanAttributeUri(Configuration $configuration, Element $element, LoggerInterface $logger)
    {
        // Check for empty attribute.
        if ($this->value === true) {
            $logger->debug(
                'Within ' . $element . ', the attribute "' . $this->name . '" requires a URI.  The value is invalid, therefore the attribute has been removed.'
            );

            return false;
        }

        /// @todo

        return true;
    }

    public function __toString()
    {
        $output = $this->name;
        if ($this->value === true) {
            return $output;
        }

        /// @todo Escape double quotes in value.
        return $output . '="' . $this->value . '"';
    }
}
