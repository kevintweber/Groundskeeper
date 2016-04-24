<?php

namespace Groundskeeper\Tokens;

interface ContainsChildren
{
    /**
     * Getter for 'children'.
     *
     * @return array
     */
    public function getChildren();

    /**
     * Hasser for 'children'.
     *
     * @param Token $token The token to search for.
     *
     * @return boolean True if $token is present.
     */
    public function hasChild(Token $token);

    public function addChild(Token $token);

    public function removeChild(Token $token);
}