<?php

namespace Groundskeeper;

use Groundskeeper\Tokens\Token;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{
    const CLEAN_STRATEGY_NONE       = 'none';
    const CLEAN_STRATEGY_STANDARD   = 'standard';
    const CLEAN_STRATEGY_AGGRESSIVE = 'aggressive';

    const ERROR_STRATEGY_NONE  = 'none';
    const ERROR_STRATEGY_THROW = 'throw';
    const ERROR_STRATEGY_FIX   = 'fix';

    const OUTPUT_COMPACT = 'compact';
    const OUTPUT_PRETTY  = 'pretty';

    const REMOVE_TYPES_NONE = 'none';

    /** @var array */
    private $options;

    /**
     * Constructor
     */
    public function __construct(array $options = array())
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);

        $this->options = $resolver->resolve($options);
        $this->setDependentOptions();
    }

    public function has($key)
    {
        return array_key_exists($key, $this->options);
    }

    public function get($key)
    {
        if (!$this->has($key)) {
            throw new \InvalidArgumentException('Invalid configuration key: ' . $key);
        }

        return $this->options[$key];
    }

    public function isAllowedType($type)
    {
        $disallowedTypeArray = explode(',', $this->options['remove-types']);
        foreach ($disallowedTypeArray as $disallowedType) {
            if (strtolower(trim($disallowedType)) == $type) {
                return false;
            }
        }

        return true;
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        // Set default options.
        $resolver->setDefaults(array(
            'clean-strategy' => self::CLEAN_STRATEGY_STANDARD,
            'error-strategy' => self::ERROR_STRATEGY_FIX,
            'indent-spaces' => 4,
            'output' => self::OUTPUT_COMPACT,
            'remove-types' => Token::CDATA . ',' . Token::COMMENT
        ));

        // Validation

        // clean-strategy
        $resolver->setAllowedTypes('clean-strategy', 'string');
        $resolver->setAllowedValues(
            'clean-strategy',
            array(
                self::CLEAN_STRATEGY_NONE,
                self::CLEAN_STRATEGY_STANDARD,
                self::CLEAN_STRATEGY_AGGRESSIVE
            )
        );

        // error-strategy
        $resolver->setAllowedTypes('error-strategy', 'string');
        $resolver->setAllowedValues(
            'error-strategy',
            array(
                self::ERROR_STRATEGY_NONE,
                self::ERROR_STRATEGY_THROW,
                self::ERROR_STRATEGY_FIX
            )
        );

        // indent-spaces
        $resolver->setAllowedTypes('indent-spaces', 'int');
        $resolver->setAllowedValues('indent-spaces', function ($value) {
                return $value >= 0;
            }
        );

        // output
        $resolver->setAllowedTypes('output', 'string');
        $resolver->setAllowedValues(
            'output',
            array(self::OUTPUT_COMPACT, self::OUTPUT_PRETTY)
        );

        // remove-types
        $resolver->setAllowedTypes('remove-types', 'string');
        $resolver->setAllowedValues(
            'remove-types',
            function ($value) {
                if ($value == self::REMOVE_TYPES_NONE) {
                    return true;
                }

                $acceptedValues = array(
                    Token::CDATA,
                    Token::COMMENT,
                    Token::DOCTYPE,
                    Token::ELEMENT,
                    Token::TEXT
                );
                $valueArray = explode(',', $value);
                foreach ($valueArray as $val) {
                    if (array_search(trim(strtolower($val)), $acceptedValues) === false) {
                        return false;
                    }
                }

                return true;
            }
        );
    }

    protected function setDependentOptions()
    {
        if ($this->options['output'] == self::OUTPUT_COMPACT) {
            $this->options['indent-spaces'] = 0;
        }
    }
}
