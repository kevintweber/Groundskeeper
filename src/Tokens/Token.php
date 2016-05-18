<?php

namespace Groundskeeper\Tokens;

interface Token
{
    const CDATA     = 'cdata';
    const COMMENT   = 'comment';
    const DOCTYPE   = 'doctype';
    const ELEMENT   = 'element';
    const PHP       = 'php';
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
     * Will return the line number of this token in the source HTML.
     *
     * @return int
     */
    public function getLine();

    /**
     * Will return the character position of this token in the source HTML.
     *
     * @return int
     */
    public function getPosition();

    /**
     * Will output the token to HTML.
     *
     * @param string $prefix
     * @param string $suffix
     *
     * @return string
     */
    public function toHtml($prefix, $suffix);
}
