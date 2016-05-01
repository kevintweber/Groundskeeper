<?php

namespace Groundskeeper\Tokens\ElementTypes;

interface InteractiveContent
{
    // https://html.spec.whatwg.org/multipage/dom.html#interactive-content-2

    // a (if the href attribute is present),
    // audio (if the controls attribute is present), button, details, embed,
    // iframe, img (if the usemap attribute is present),
    // input (if the type attribute is not in the hidden state), keygen,
    // label, object (if the usemap attribute is present), select, textarea,
    // video (if the controls attribute is present)
}
