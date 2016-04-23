<?php

namespace Groundskeeper;

use Groundskeeper\Tokens\Tokenizer;
use Symfony\Component\OptionsResolver\OptionsResolver;

class Groundskeeper
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
    }

    public function clean($html)
    {
        // Tokenize
        $tokenizer = new Tokenizer($this->options);
        $tokens = $tokenizer->tokenize($html);

        // Clean

        // Output
        $outputClassName = 'Groundskeeper\\Output\\' .
            ucfirst($this->options['output']);
        $output = new $outputClassName();

        return $output->printTokens($tokens);
    }

    public function getOptions()
    {
        return $this->options;
    }

    protected function configureOptions(OptionsResolver $resolver)
    {
        // Set default options.
        $resolver->setDefaults(array(
            'indent-spaces' => 4,
            'output' => 'compact',
            'throw-on-error' => false
        ));

        // Validation
        $resolver->setAllowedValues('indent-spaces', function ($value) {
            if (!is_int($value)) {
                return false;
            }

            return $value >= 0;
        });
        $resolver->setAllowedValues('output', array('compact', 'pretty'));
        $resolver->setAllowedTypes('throw-on-error', 'bool');
    }
}
