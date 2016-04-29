<?php

namespace Groundskeeper\Tokens\Elements;

use Groundskeeper\Configuration;
use Groundskeeper\Exceptions\ValidationException;
use Groundskeeper\Tokens\AbstractToken;
use Groundskeeper\Tokens\Cleanable;
use Groundskeeper\Tokens\ContainsChildren;
use Groundskeeper\Tokens\Removable;
use Groundskeeper\Tokens\Token;
use Psr\Log\LoggerInterface;

class Element extends AbstractToken implements Cleanable, ContainsChildren, Removable
{
    const ATTR_CI_ENUM   = 'ci_enu';// case-insensitive enumeration
    const ATTR_JS        = 'cs_jsc';
    const ATTR_CI_STRING = 'ci_str'; // case-insensitive string
    const ATTR_CS_STRING = 'cs_str'; // case-sensitive string
    const ATTR_URI       = 'cs_uri';

    /** @var array */
    protected $attributes;

    /** @var array[Token] */
    protected $children;

    /** @var string */
    private $name;

    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, $name, array $attributes = array(), $parent = null)
    {
        parent::__construct(Token::ELEMENT, $configuration, $parent);

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
     * Required by ContainsChildren interface.
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Required by ContainsChildren interface.
     */
    public function hasChild(Token $token)
    {
        return array_search($token, $this->children) !== false;
    }

    /**
     * Required by ContainsChildren interface.
     */
    public function appendChild(Token $token)
    {
        $token->setParent($this);
        $this->children[] = $token;

        return $this;
    }

    /**
     * Required by ContainsChildren interface.
     */
    public function prependChild(Token $token)
    {
        $token->setParent($this);
        array_unshift($this->children, $token);

        return $this;
    }

    /**
     * Required by the ContainsChildren interface.
     */
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

    /**
     * Required by the Cleanable interface.
     */
    public function clean(LoggerInterface $logger = null)
    {
        if ($this->configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        // Remove non-standard attributes.
        foreach ($this->attributes as $name => $value) {
            $attributeParameters = $this->getAttributeParameters($name);
            if (empty($attributeParameters)) {
                if ($logger !== null) {
                    $logger->debug('Groundskeeper: Removed non-standard attribute "' . $name . '" from element "' . $this->name . '".');
                }

                unset($this->attributes[$name]);

                continue;
            }

            // Validate attribute value.
            list($caseSensitivity, $attributeType) =
                explode('_', $attributeParameters['valueType']);

            // Handle case-insensitivity.
            // Standard is case-insensitive attribute values should be lower case.
            // Not required, so don't throw if out of spec.
            if ($caseSensitivity == 'ci') {
                $newValue = strtolower($value);
                if ($newValue !== $value) {
                    if ($this->configuration->get('error-strategy') == Configuration::ERROR_STRATEGY_FIX) {
                        $this->attributes[$name] = $newValue;
                        if ($logger !== null) {
                            $logger->debug('Groundskeeper: The value for the attribute "' . $name . '" is case-insensitive.  The value has been converted to lower case.');
                        }
                    } elseif ($logger !== null) {
                        $logger->debug('Groundskeeper: The value for the attribute "' . $name . '" is case-insensitive.  Consider converting it to lower case.');
                    }
                }
            }

            switch (substr($attributeType, 0, 3)) {
            case 'enu': // enumeration
                /// @todo
                break;

            case 'uri': // URI
                /// @todo
                break;
            }
        }

        // Clean children.
        return AbstractToken::cleanChildTokens(
            $this->configuration,
            $this->children,
            $logger
        );
    }

    protected function getAllowedAttributes()
    {
        return array(
            // Global Attributes
            '/^accesskey$/i' => self::ATTR_CS_STRING,
            '/^class$/i' => self::ATTR_CS_STRING,
            '/^contenteditable$/i' => self::ATTR_CS_STRING,
            '/^contextmenu$/i' => self::ATTR_CS_STRING,
            '/^data-\S/i' => self::ATTR_CS_STRING,
            '/^dir$/i' => self::ATTR_CI_ENUM . '("ltr","rtl"|"ltr")',
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
            '/^translate$/i' => self::ATTR_CI_ENUM . '("yes","no",""|"yes")',

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

    protected function getAttributeParameters($name)
    {
        $allowedAttributes = $this->getAllowedAttributes();
        foreach ($allowedAttributes as $attrRegex => $valueType) {
            if (preg_match($attrRegex, $name) === 1) {
                return array(
                    'name' => $name,
                    'regex' => $attrRegex,
                    'valueType' => $valueType
                );
            }
        }

        return array();
    }

    /**
     * Required by the Removable interface.
     */
    public function remove(LoggerInterface $logger = null)
    {
        $hasRemovableTypes = $this->configuration->get('type-blacklist') !==
            Configuration::TYPE_BLACKLIST_NONE;
        $hasRemovableElements = $this->configuration->get('element-blacklist') !==
            Configuration::ELEMENT_BLACKLIST_NONE;
        foreach ($this->children as $key => $child) {
            // Check types.
            if ($hasRemovableTypes &&
                !$this->configuration->isAllowedType($child->getType())) {
                unset($this->children[$key]);
                if ($logger !== null) {
                    $logger->debug('Removing token of type: ' . $child->getType());
                }

                continue;
            }

            // Check elements.
            if ($hasRemovableElements &&
                $child instanceof Element &&
                !$this->configuration->isAllowedElement($child->getName())) {
                unset($this->children[$key]);
                if ($logger !== null) {
                    $logger->debug('Removing element of type: ' . $child->getName());
                }

                continue;
            }

            // Check children.
            if ($child instanceof Removable) {
                $child->remove($logger);
            }
        }
    }

    /**
     * Required by the Token interface.
     */
    public function toHtml($prefix, $suffix)
    {
        $output = $this->buildStartTag($prefix, $suffix);
        if (empty($this->children)) {
            return $output;
        }

        $output .= $this->buildChildrenHtml($prefix, $suffix);

        return $output . $prefix . '</' . $this->name . '>' . $suffix;
    }

    protected function buildStartTag($prefix, $suffix, $forceOpen = false)
    {
        $output = $prefix . '<' . $this->name;
        foreach ($this->attributes as $key => $value) {
            $output .= ' ' . strtolower($key);
            if (is_string($value)) {
                /// @todo Escape double quotes in value.
                $output .= '="' . $value . '"';
            }
        }

        if (!$forceOpen && empty($this->children)) {
            return $output . '/>' . $suffix;
        }

        return $output . '>' . $suffix;
    }

    protected function buildChildrenHtml($prefix, $suffix)
    {
        $output = '';
        foreach ($this->children as $child) {
            $newPrefix = $prefix .
                str_repeat(
                    ' ',
                    $this->configuration->get('indent-spaces')
                );
            $output .= $child->toHtml($newPrefix, $suffix);
        }

        return $output;
    }
}
