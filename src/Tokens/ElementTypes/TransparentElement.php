<?php

namespace Groundskeeper\Tokens\ElementTypes;

/**
 * Transparent elements
 *
 * https://html.spec.whatwg.org/multipage/dom.html#transparent
 * http://andowebsit.es/blog/noteslog.com/post/html5-transparent-elements/
 */
interface TransparentElement
{
    public function isTransparentElement() : bool;
}
