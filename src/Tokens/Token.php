<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;

interface Token
{
    const CDATA     = 'cdata';
    const COMMENT   = 'comment';
    const DOCTYPE   = 'doctype';
    const ELEMENT   = 'element';
    const TEXT      = 'text';

    /**
     * Will return the nesting depth of this token.
     *
     * @return int
     */
    public function getDepth();

    /**
     * Will return the parent token.
     *
     * @return null|Token
     */
    public function getParent();

    /**
     * Will return the type of token.
     *
     * @return string
     */
    public function getType();

    /**
     * Will internally create a valid token.
     */
    public function validate(Configuration $configuration);

    /**
     * Will output the token to text.
     *
     * @param Configuration $configuration
     * @param string        $prefix
     * @param string        $suffix
     *
     * @return string
     */
    public function toString(Configuration $configuration, $prefix = '', $suffix = '');
}
