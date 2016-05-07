<?php

namespace Groundskeeper;

use Groundskeeper\Tokens\Token;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{
    const CLEAN_STRATEGY_NONE       = 'none';
    const CLEAN_STRATEGY_LENIENT    = 'lenient';
    const CLEAN_STRATEGY_STANDARD   = 'standard';
    const CLEAN_STRATEGY_AGGRESSIVE = 'aggressive';

    const OUTPUT_COMPACT = 'compact';
    const OUTPUT_PRETTY  = 'pretty';

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

    public function isAllowedElement($element)
    {
        $disallowedElementArray = explode(',', $this->options['element-blacklist']);

        return array_search($element, $disallowedElementArray) === false;
    }

    public function isAllowedType($type)
    {
        $disallowedTypeArray = explode(',', $this->options['type-blacklist']);

        return array_search($type, $disallowedTypeArray) === false;
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        // Set default options.
        $resolver->setDefaults(array(
            'clean-strategy' => self::CLEAN_STRATEGY_STANDARD,
            'element-blacklist' => '',
            'indent-spaces' => 4,
            'output' => self::OUTPUT_COMPACT,
            'type-blacklist' => Token::CDATA . ',' . Token::COMMENT
        ));

        // Validation

        // clean-strategy
        $resolver->setAllowedTypes('clean-strategy', 'string');
        $resolver->setAllowedValues(
            'clean-strategy',
            array(
                self::CLEAN_STRATEGY_NONE,
                self::CLEAN_STRATEGY_LENIENT,
                self::CLEAN_STRATEGY_STANDARD,
                self::CLEAN_STRATEGY_AGGRESSIVE
            )
        );

        // element-blacklist
        $resolver->setAllowedTypes('element-blacklist', 'string');
        $resolver->setNormalizer(
            'element-blacklist',
            function (Options $options, $value) {
                $valueArray = explode(',', $value);
                $formattedValueArray = array();
                foreach ($valueArray as $data) {
                    $formattedValueArray[] = trim(strtolower($data));
                }

                return implode(',', $formattedValueArray);
            }
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

        // type-blacklist
        $resolver->setAllowedTypes('type-blacklist', 'string');
        $resolver->setAllowedValues(
            'type-blacklist',
            function ($value) {
                if ($value == '') {
                    return true;
                }

                $acceptedValues = array(
                    Token::CDATA,
                    Token::COMMENT,
                    Token::DOCTYPE,
                    Token::ELEMENT,
                    Token::PHP,
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
        $resolver->setNormalizer(
            'type-blacklist',
            function (Options $options, $value) {
                $valueArray = explode(',', $value);
                $formattedValueArray = array();
                foreach ($valueArray as $data) {
                    $formattedValueArray[] = trim(strtolower($data));
                }

                return implode(',', $formattedValueArray);
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
