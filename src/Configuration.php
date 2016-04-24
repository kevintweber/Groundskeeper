<?php

namespace Groundskeeper;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Configuration
{
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
            'clean-strategy' => 'standard',
            'error-strategy' => 'fix',
            'indent-spaces' => 4,
            'output' => 'compact',
            'remove-types' => 'cdata,comment'
        ));

        // Validation

        // clean-strategy
        $resolver->setAllowedTypes('clean-strategy', 'string');
        $resolver->setAllowedValues('clean-strategy', array('none', 'standard'));

        // error-strategy
        $resolver->setAllowedTypes('error-strategy', 'string');
        $resolver->setAllowedValues('error-strategy', array('none', 'throw', 'fix'));

        // indent-spaces
        $resolver->setAllowedTypes('indent-spaces', 'int');
        $resolver->setAllowedValues('indent-spaces', function ($value) {
                return $value >= 0;
            }
        );

        // output
        $resolver->setAllowedTypes('output', 'string');
        $resolver->setAllowedValues('output', array('compact', 'pretty'));

        // remove-types
        $resolver->setAllowedTypes('remove-types', 'string');
        $resolver->setAllowedValues('remove-types', function ($value) {
                if ($value == 'none') {
                    return true;
                }

                $acceptedValues = array(
                    'cdata',
                    'comment',
                    'doctype',
                    'element',
                    'text'
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
        if ($this->options['output'] == 'compact') {
            $this->options['indent-spaces'] = 0;
        }
    }
}
