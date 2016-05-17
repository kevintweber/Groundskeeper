<?php

namespace Groundskeeper\Tokens\ElementTypes;

/**
 * https://html.spec.whatwg.org/multipage/dom.html#phrasing-content-2
 */
interface PhrasingContent
{
    // a, abbr, area (if it is a descendant of a map element), audio, b,
    // bdi, bdo, br, button, canvas, cite, code, data, datalist, del, dfn,
    // em, embed, i, iframe, img, input, ins, kbd, keygen, label,
    // link (if it is allowed in the body), map, mark, <mathml> math,
    // meta (if the itemprop attribute is present), meter, noscript,
    // object, output, picture, progress, q, ruby, s, samp, script, select,
    // slot, small, span, strong, sub, sup, <svg> svg, template, textarea,
    // time, u, var, video, wbr, <autonomous custom elements>, <text>
}
