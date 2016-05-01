<?php

namespace Groundskeeper\Tokens\ElementTypes;

interface FlowContent
{
    // https://html.spec.whatwg.org/multipage/dom.html#flow-content-2

    // a, abbr, address, area (if it is a descendant of a map element),
    // article, aside, audio, b, bdi, bdo, blockquote, br, button,
    // canvas, cite, code, data, datalist, del, details, dfn, dialog,
    // div, dl, em, embed, fieldset, figure, footer, form, h1, h2, h3,
    // h4, h5, h6, header, hgroup, hr, i, iframe, img, input, ins, kbd,
    // keygen, label, link (if it is allowed in the body), main, map,
    // mark, <mathml> math, menu, meta (if the itemprop attribute is present),
    // meter, nav, noscript, object, ol, output, p, picture, pre, progress, q,
    // ruby, s, samp, script, section, select, slot, small, span, strong,
    // style (if the scoped attribute is present), sub, sup, <svg> svg,
    // table, template, textarea, time, u, ul, var, video, wbr,
    // <autonomous custom elements>, <text>
}
