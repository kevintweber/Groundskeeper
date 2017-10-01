<?php

namespace Groundskeeper\Tokens;

interface ContainsChildren
{
    public function getChildren() : array;

    public function hasChild(Token $token) : bool;

    public function appendChild(Token $token);

    public function prependChild(Token $token);

    public function removeChild(Token $token);
}
