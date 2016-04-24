<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Exceptions\ValidationException;
use Groundskeeper\Tokens\AbstractToken;
use Groundskeeper\Tokens\Token;

class Element extends AbstractToken
{
    const ATTR_CI_ENUM   = 'attr_ci_enum';// case-insensitive enumeration
    const ATTR_JS        = 'attr_js';
    const ATTR_CI_STRING = 'attr_ci_str'; // case-insensitive string
    const ATTR_CS_STRING = 'attr_cs_str'; // case-sensitive string
    const ATTR_URI       = 'attr_uri';

    /** @var array */
    private $attributes;

    /** @var array[Token] */
    private $children;

    /** @var string */
    private $name;

    /**
     * Constructor
     */
    public function __construct($name, array $attributes = array(), $parent = null)
    {
        parent::__construct(Token::ELEMENT, $parent);

        $this->attributes = array();
        foreach ($attributes as $key => $value) {
            $this->addAttribute($key, $value);
        }

        $this->children = array();
        $this->setName($name);
    }

    /**
     * Getter for 'attributes'.
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * Hasser for 'attributes'.
     *
     * @param string $key
     *
     * @return boolean True if the attribute is present.
     */
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    public function addAttribute($key, $value)
    {
        $key = trim(strtolower($key));
        if ($key == '') {
            throw new \InvalidArgumentException('Invalid emtpy attribute key.');
        }

        $this->attributes[$key] = $value;

        return $this;
    }

    public function removeAttribute($key)
    {
        $key = strtolower($key);
        if (isset($this->attributes[$key])) {
            unset($this->attributes[$key]);

            return true;
        }

        return false;
    }

    /**
     * Getter for 'children'.
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function addChild(Token $token)
    {
        $token->setParent($this);
        $this->children[] = $token;

        return $this;
    }

    public function removeChild(Token $token)
    {
        $key = array_search($token, $this->children);
        if ($key !== false) {
            unset($this->children[$key]);

            return true;
        }

        return false;
    }

    /**
     * Getter for 'name'.
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Chainable setter for 'name'.
     */
    public function setName($name)
    {
        if (!is_string($name)) {
            throw new \InvalidArgumentException('Element name must be string type.');
        }

        $this->name = trim(strtolower($name));

        return $this;
    }

    protected function getAllowedAttrbutes()
    {
        return array(
            // Global Attributes
            '/^accesskey$/i' => self::ATTR_CS_STRING,
            '/^class$/i' => self::ATTR_CS_STRING,
            '/^contenteditable$/i' => self::ATTR_CS_STRING,
            '/^contextmenu$/i' => self::ATTR_CS_STRING,
            '/^data-\S/i' => self::ATTR_CS_STRING,
            '/^dir$/i' => self::ATTR_CI_ENUM . '("ltr","rtl")',
            '/^draggable$/i' => self::ATTR_CS_STRING,
            '/^dropzone$/i' => self::ATTR_CS_STRING,
            '/^hidden$/i' => self::ATTR_CS_STRING,
            '/^id$/i' => self::ATTR_CS_STRING,
            '/^is$/i' => self::ATTR_CS_STRING,
            '/^itemid$/i' => self::ATTR_CS_STRING,
            '/^itemprop$/i' => self::ATTR_CS_STRING,
            '/^itemref$/i' => self::ATTR_CS_STRING,
            '/^itemscope$/i' => self::ATTR_CS_STRING,
            '/^itemtype$/i' => self::ATTR_CS_STRING,
            '/^lang$/i' => self::ATTR_CI_STRING,
            '/^slot$/i' => self::ATTR_CS_STRING,
            '/^spellcheck$/i' => self::ATTR_CS_STRING,
            '/^style$/i' => self::ATTR_CS_STRING,
            '/^tabindex$/i' => self::ATTR_CS_STRING,
            '/^title$/i' => self::ATTR_CS_STRING,
            '/^translate$/i' => self::ATTR_CI_ENUM . '("yes","no","")',

            // Event Handler Content Attributes
            // https://html.spec.whatwg.org/multipage/webappapis.html#event-handler-content-attributes
            '/^onabort$/i' => self::ATTR_JS,
            '/^onautocomplete$/i' => self::ATTR_JS,
            '/^onautocompleteerror$/i' => self::ATTR_JS,
            '/^onblur$/i' => self::ATTR_JS,
            '/^oncancel$/i' => self::ATTR_JS,
            '/^oncanplay$/i' => self::ATTR_JS,
            '/^oncanplaythrough$/i' => self::ATTR_JS,
            '/^onchange$/i' => self::ATTR_JS,
            '/^onclick$/i' => self::ATTR_JS,
            '/^onclose$/i' => self::ATTR_JS,
            '/^oncontextmenu$/i' => self::ATTR_JS,
            '/^oncuechange$/i' => self::ATTR_JS,
            '/^ondblclick$/i' => self::ATTR_JS,
            '/^ondrag$/i' => self::ATTR_JS,
            '/^ondragend$/i' => self::ATTR_JS,
            '/^ondragenter$/i' => self::ATTR_JS,
            '/^ondragexit$/i' => self::ATTR_JS,
            '/^ondragleave$/i' => self::ATTR_JS,
            '/^ondragover$/i' => self::ATTR_JS,
            '/^ondragstart$/i' => self::ATTR_JS,
            '/^ondrop$/i' => self::ATTR_JS,
            '/^ondurationchange$/i' => self::ATTR_JS,
            '/^onemptied$/i' => self::ATTR_JS,
            '/^onended$/i' => self::ATTR_JS,
            '/^onerror$/i' => self::ATTR_JS,
            '/^onfocus$/i' => self::ATTR_JS,
            '/^oninput$/i' => self::ATTR_JS,
            '/^oninvalid$/i' => self::ATTR_JS,
            '/^onkeydown$/i' => self::ATTR_JS,
            '/^onkeypress$/i' => self::ATTR_JS,
            '/^onkeyup$/i' => self::ATTR_JS,
            '/^onload$/i' => self::ATTR_JS,
            '/^onloadeddata$/i' => self::ATTR_JS,
            '/^onloadedmetadata$/i' => self::ATTR_JS,
            '/^onloadstart$/i' => self::ATTR_JS,
            '/^onmousedown$/i' => self::ATTR_JS,
            '/^onmouseenter$/i' => self::ATTR_JS,
            '/^onmouseleave$/i' => self::ATTR_JS,
            '/^onmousemove$/i' => self::ATTR_JS,
            '/^onmouseout$/i' => self::ATTR_JS,
            '/^onmouseover$/i' => self::ATTR_JS,
            '/^onmouseup$/i' => self::ATTR_JS,
            '/^onwheel$/i' => self::ATTR_JS,
            '/^onpause$/i' => self::ATTR_JS,
            '/^onplay$/i' => self::ATTR_JS,
            '/^onplaying$/i' => self::ATTR_JS,
            '/^onprogress$/i' => self::ATTR_JS,
            '/^onratechange$/i' => self::ATTR_JS,
            '/^onreset$/i' => self::ATTR_JS,
            '/^onresize$/i' => self::ATTR_JS,
            '/^onscroll$/i' => self::ATTR_JS,
            '/^onseeked$/i' => self::ATTR_JS,
            '/^onseeking$/i' => self::ATTR_JS,
            '/^onselect$/i' => self::ATTR_JS,
            '/^onshow$/i' => self::ATTR_JS,
            '/^onstalled$/i' => self::ATTR_JS,
            '/^onsubmit$/i' => self::ATTR_JS,
            '/^onsuspend$/i' => self::ATTR_JS,
            '/^ontimeupdate$/i' => self::ATTR_JS,
            '/^ontoggle$/i' => self::ATTR_JS,
            '/^onvolumechange$/i' => self::ATTR_JS,
            '/^onwaiting$/i' => self::ATTR_JS,

            // WAI-ARIA
            // https://w3c.github.io/aria/aria/aria.html
            '/^role$/i' => self::ATTR_CI_STRING,

            // ARIA global states and properties
            '/^aria-atomic$/i' => self::ATTR_CS_STRING,
            '/^aria-busy$/i' => self::ATTR_CS_STRING,
            '/^aria-controls$/i' => self::ATTR_CS_STRING,
            '/^aria-current$/i' => self::ATTR_CS_STRING,
            '/^aria-describedby$/i' => self::ATTR_CS_STRING,
            '/^aria-details$/i' => self::ATTR_CS_STRING,
            '/^aria-disabled$/i' => self::ATTR_CS_STRING,
            '/^aria-dropeffect$/i' => self::ATTR_CS_STRING,
            '/^aria-errormessage$/i' => self::ATTR_CS_STRING,
            '/^aria-flowto$/i' => self::ATTR_CS_STRING,
            '/^aria-grabbed$/i' => self::ATTR_CS_STRING,
            '/^aria-haspopup$/i' => self::ATTR_CS_STRING,
            '/^aria-hidden$/i' => self::ATTR_CS_STRING,
            '/^aria-invalid$/i' => self::ATTR_CS_STRING,
            '/^aria-label$/i' => self::ATTR_CS_STRING,
            '/^aria-labelledby$/i' => self::ATTR_CS_STRING,
            '/^aria-live$/i' => self::ATTR_CS_STRING,
            '/^aria-owns$/i' => self::ATTR_CS_STRING,
            '/^aria-relevant$/i' => self::ATTR_CS_STRING,
            '/^aria-roledescription$/i' => self::ATTR_CS_STRING,

            // ARIA widget attributes
            '/^aria-autocomplete$/i' => self::ATTR_CS_STRING,
            '/^aria-checked$/i' => self::ATTR_CS_STRING,
            '/^aria-expanded$/i' => self::ATTR_CS_STRING,
            '/^aria-level$/i' => self::ATTR_CS_STRING,
            '/^aria-modal$/i' => self::ATTR_CS_STRING,
            '/^aria-multiline$/i' => self::ATTR_CS_STRING,
            '/^aria-multiselectable$/i' => self::ATTR_CS_STRING,
            '/^aria-orientation$/i' => self::ATTR_CS_STRING,
            '/^aria-placeholder$/i' => self::ATTR_CS_STRING,
            '/^aria-pressed$/i' => self::ATTR_CS_STRING,
            '/^aria-readonly$/i' => self::ATTR_CS_STRING,
            '/^aria-required$/i' => self::ATTR_CS_STRING,
            '/^aria-selected$/i' => self::ATTR_CS_STRING,
            '/^aria-sort$/i' => self::ATTR_CS_STRING,
            '/^aria-valuemax$/i' => self::ATTR_CS_STRING,
            '/^aria-valuemin$/i' => self::ATTR_CS_STRING,
            '/^aria-valuenow$/i' => self::ATTR_CS_STRING,
            '/^aria-valuetext$/i' => self::ATTR_CS_STRING,

            // ARIA relationship attributes
            '/^aria-activedescendant$/i' => self::ATTR_CS_STRING,
            '/^aria-colcount$/i' => self::ATTR_CS_STRING,
            '/^aria-colindex$/i' => self::ATTR_CS_STRING,
            '/^aria-colspan$/i' => self::ATTR_CS_STRING,
            '/^aria-posinset$/i' => self::ATTR_CS_STRING,
            '/^aria-rowcount$/i' => self::ATTR_CS_STRING,
            '/^aria-rowindex$/i' => self::ATTR_CS_STRING,
            '/^aria-rowspan$/i' => self::ATTR_CS_STRING,
            '/^aria-setsize$/i' => self::ATTR_CS_STRING
        );
    }

    protected function isAttributeNameValid($name)
    {
        $allowedAttributes = $this->getAllowedAttrbutes();
        foreach ($allowedAttributes as $attrRegex => $valueType) {
            if (preg_match($attrRegex, $name) === 1) {
                return true;
            }
        }

        return false;
    }

    public function validate(Configuration $configuration)
    {
        if ($configuration->get('clean-strategy') == 'none') {
            $this->isValid = true;
            foreach ($this->children as $child) {
                $child->validate($configuration);
            }

            return;
        }

        parent::validate($configuration);

        // If not valid, then we are done.
        if (!$this->isValid) {
            return;
        }

        if ($configuration->get('clean-strategy') != 'none') {
            // Remove non-standard attributes.
            foreach ($this->attributes as $name => $value) {
                // Validate attribute name
                if (!$this->isAttributeNameValid($name)) {
                    unset($this->attributes[$name]);
                    continue;
                }

                // Validate attributes value
                /// @todo
            }
        }

        foreach ($this->children as $child) {
            $child->validate($configuration);
        }
    }

    protected function handleValidationError(Configuration $configuration, $message)
    {
        $this->isValid = false;
        if ($configuration->get('error-strategy') == 'throw') {
            throw new ValidationException($message);
        }
    }

    public function toString(Configuration $configuration, $prefix = '', $suffix = '')
    {
        if (!$this->isValid && $configuration->get('clean-strategy') != 'none') {
            return '';
        }

        $output = $this->toStringTag($configuration, $prefix, $suffix);
        if (empty($this->children)) {
            return $output;
        }

        foreach ($this->children as $child) {
            $newPrefix = $prefix . str_repeat(' ', $configuration->get('indent-spaces'));
            $output .= $child->toString($configuration, $newPrefix, $suffix);
        }

        return $output . $prefix . '</' . $this->name . '>' . $suffix;
    }

    protected function toStringTag(Configuration $configuration, $prefix = '', $suffix = '', $forceOpen = false)
    {
        $output = $prefix . '<' . $this->name;
        foreach ($this->attributes as $key => $value) {
            $output .= ' ' . strtolower($key);
            if (is_string($value)) {
                $output .= '="' . $value . '"';
            }
        }

        if (!$forceOpen && empty($this->children)) {
            return $output . '/>' . $suffix;
        }

        return $output . '>' . $suffix;
    }
}
