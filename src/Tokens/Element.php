<?php

namespace Groundskeeper\Tokens;

use Groundskeeper\Configuration;
use Psr\Log\LoggerInterface;

class Element extends AbstractToken implements Cleanable, ContainsChildren, Removable
{
    /** @var array */
    protected $attributes;

    /** @var array[Token] */
    protected $children;

    /** @var string */
    private $name;

    /**
     * Constructor
     */
    public function __construct(Configuration $configuration, $name, array $attributes = array())
    {
        parent::__construct($configuration);

        $this->attributes = array();
        foreach ($attributes as $key => $value) {
            $this->addAttribute($key, $value);
        }

        $this->children = array();

        if (!is_string($name)) {
            throw new \InvalidArgumentException('Element name must be string type.');
        }

        $this->name = trim(strtolower($name));
    }

    /**
     * Getter for 'attributes'.
     */
    public function getAttributes()
    {
        $attributeArray = array();
        foreach ($this->attributes as $attribute) {
            $attributeArray[$attribute->getName()] = $attribute->getValue();
        }

        return $attributeArray;
    }

    public function getAttribute($key)
    {
        if (!$this->hasAttribute($key)) {
            throw new \InvalidArgumentException('Invalid attribute key: ' . $key);
        }

        $attributeObject = $this->attributes[$key];

        return $attributeObject->getValue();
    }

    /**
     * Hasser for 'attributes'.
     *
     * @param string $key
     *
     * @return bool True if the attribute is present.
     */
    public function hasAttribute($key)
    {
        return array_key_exists($key, $this->attributes);
    }

    public function addAttribute($key, $value)
    {
        $key = trim(strtolower($key));
        if ($key == '') {
            throw new \InvalidArgumentException('Invalid empty attribute key.');
        }

        $attributeParameters = $this->getAttributeParameters($key);
        $isStandard = true;
        if (empty($attributeParameters)) {
            $attributeParameters = array(
                'name' => $key,
                'regex' => '/\S*/i',
                'valueType' => Attribute::CS_STRING
            );
            $isStandard = false;
        }

        $this->attributes[$key] = new Attribute(
            $key,
            $value,
            $attributeParameters['valueType'],
            $isStandard
        );

        return $this;
    }

    public function removeAttribute($key)
    {
        $key = trim(strtolower($key));
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
        return array_search($token, $this->children, true) !== false;
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
        $key = array_search($token, $this->children, true);
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
     * Required by the Cleanable interface.
     */
    public function clean(LoggerInterface $logger)
    {
        if ($this->configuration->get('clean-strategy') == Configuration::CLEAN_STRATEGY_NONE) {
            return true;
        }

        // Assign attributes to the attributes. (Soooo meta ....)
        foreach ($this->attributes as $attribute) {
            $attributeParameters = $this->getAttributeParameters(
                $attribute->getName()
            );
            $isStandard = true;
            if (empty($attributeParameters)) {
                $attributeParameters = array(
                    'name' => $attribute->getName(),
                    'regex' => '/\S*/i',
                    'valueType' => Attribute::UNKNOWN
                );
                $isStandard = false;
            }

            $attribute->setType($attributeParameters['valueType']);
            $attribute->setIsStandard($isStandard);
        }

        // Clean attributes.
        foreach ($this->attributes as $attribute) {
            $attributeCleanResult = $attribute->clean(
                $this->configuration,
                $this,
                $logger
            );
            if (!$attributeCleanResult && $this->configuration->get('clean-strategy') !== Configuration::CLEAN_STRATEGY_LENIENT) {
                unset($this->attributes[$attribute->getName()]);
            }
        }

        // Fix self (if possible)
        $this->fixSelf($logger);

        // Remove self or children?
        if ($this->configuration->get('clean-strategy') !== Configuration::CLEAN_STRATEGY_LENIENT) {
            // Remove self?
            if ($this->removeInvalidSelf($logger)) {
                return false;
            }

            // Remove children?
            $this->removeInvalidChildren($logger);
        }

        // Clean children.
        return AbstractToken::cleanChildTokens(
            $this->configuration,
            $this->children,
            $logger
        );
    }

    protected function fixSelf(LoggerInterface $logger)
    {
    }

    protected function removeInvalidChildren(LoggerInterface $logger)
    {
    }

    protected function removeInvalidSelf(LoggerInterface $logger)
    {
        return false;
    }

    protected function getAllowedAttributes()
    {
        return array(
            // Global Attributes
            '/^accesskey$/i' => Attribute::CS_STRING,
            '/^class$/i' => Attribute::CS_STRING,
            '/^contenteditable$/i' => Attribute::CS_STRING,
            '/^contextmenu$/i' => Attribute::CS_STRING,
            '/^data-\S/i' => Attribute::CS_STRING,
            '/^dir$/i' => Attribute::CI_ENUM . '("ltr","rtl"|"ltr")',
            '/^draggable$/i' => Attribute::CS_STRING,
            '/^dropzone$/i' => Attribute::CS_STRING,
            '/^hidden$/i' => Attribute::CS_STRING,
            '/^id$/i' => Attribute::CS_STRING,
            '/^is$/i' => Attribute::CS_STRING,
            '/^itemid$/i' => Attribute::CS_STRING,
            '/^itemprop$/i' => Attribute::CS_STRING,
            '/^itemref$/i' => Attribute::CS_STRING,
            '/^itemscope$/i' => Attribute::CS_STRING,
            '/^itemtype$/i' => Attribute::CS_STRING,
            '/^lang$/i' => Attribute::CI_STRING,
            '/^slot$/i' => Attribute::CS_STRING,
            '/^spellcheck$/i' => Attribute::CS_STRING,
            '/^style$/i' => Attribute::CS_STRING,
            '/^tabindex$/i' => Attribute::CS_STRING,
            '/^title$/i' => Attribute::CS_STRING,
            '/^translate$/i' => Attribute::CI_ENUM . '("yes","no",""|"yes")',

            // Event Handler Content Attributes
            // https://html.spec.whatwg.org/multipage/webappapis.html#event-handler-content-attributes
            '/^onabort$/i' => Attribute::JS,
            '/^onautocomplete$/i' => Attribute::JS,
            '/^onautocompleteerror$/i' => Attribute::JS,
            '/^onblur$/i' => Attribute::JS,
            '/^oncancel$/i' => Attribute::JS,
            '/^oncanplay$/i' => Attribute::JS,
            '/^oncanplaythrough$/i' => Attribute::JS,
            '/^onchange$/i' => Attribute::JS,
            '/^onclick$/i' => Attribute::JS,
            '/^onclose$/i' => Attribute::JS,
            '/^oncontextmenu$/i' => Attribute::JS,
            '/^oncuechange$/i' => Attribute::JS,
            '/^ondblclick$/i' => Attribute::JS,
            '/^ondrag$/i' => Attribute::JS,
            '/^ondragend$/i' => Attribute::JS,
            '/^ondragenter$/i' => Attribute::JS,
            '/^ondragexit$/i' => Attribute::JS,
            '/^ondragleave$/i' => Attribute::JS,
            '/^ondragover$/i' => Attribute::JS,
            '/^ondragstart$/i' => Attribute::JS,
            '/^ondrop$/i' => Attribute::JS,
            '/^ondurationchange$/i' => Attribute::JS,
            '/^onemptied$/i' => Attribute::JS,
            '/^onended$/i' => Attribute::JS,
            '/^onerror$/i' => Attribute::JS,
            '/^onfocus$/i' => Attribute::JS,
            '/^oninput$/i' => Attribute::JS,
            '/^oninvalid$/i' => Attribute::JS,
            '/^onkeydown$/i' => Attribute::JS,
            '/^onkeypress$/i' => Attribute::JS,
            '/^onkeyup$/i' => Attribute::JS,
            '/^onload$/i' => Attribute::JS,
            '/^onloadeddata$/i' => Attribute::JS,
            '/^onloadedmetadata$/i' => Attribute::JS,
            '/^onloadstart$/i' => Attribute::JS,
            '/^onmousedown$/i' => Attribute::JS,
            '/^onmouseenter$/i' => Attribute::JS,
            '/^onmouseleave$/i' => Attribute::JS,
            '/^onmousemove$/i' => Attribute::JS,
            '/^onmouseout$/i' => Attribute::JS,
            '/^onmouseover$/i' => Attribute::JS,
            '/^onmouseup$/i' => Attribute::JS,
            '/^onwheel$/i' => Attribute::JS,
            '/^onpause$/i' => Attribute::JS,
            '/^onplay$/i' => Attribute::JS,
            '/^onplaying$/i' => Attribute::JS,
            '/^onprogress$/i' => Attribute::JS,
            '/^onratechange$/i' => Attribute::JS,
            '/^onreset$/i' => Attribute::JS,
            '/^onresize$/i' => Attribute::JS,
            '/^onscroll$/i' => Attribute::JS,
            '/^onseeked$/i' => Attribute::JS,
            '/^onseeking$/i' => Attribute::JS,
            '/^onselect$/i' => Attribute::JS,
            '/^onshow$/i' => Attribute::JS,
            '/^onstalled$/i' => Attribute::JS,
            '/^onsubmit$/i' => Attribute::JS,
            '/^onsuspend$/i' => Attribute::JS,
            '/^ontimeupdate$/i' => Attribute::JS,
            '/^ontoggle$/i' => Attribute::JS,
            '/^onvolumechange$/i' => Attribute::JS,
            '/^onwaiting$/i' => Attribute::JS,

            // WAI-ARIA
            // https://w3c.github.io/aria/aria/aria.html
            '/^role$/i' => Attribute::CI_STRING,

            // ARIA global states and properties
            '/^aria-atomic$/i' => Attribute::CS_STRING,
            '/^aria-busy$/i' => Attribute::CS_STRING,
            '/^aria-controls$/i' => Attribute::CS_STRING,
            '/^aria-current$/i' => Attribute::CS_STRING,
            '/^aria-describedby$/i' => Attribute::CS_STRING,
            '/^aria-details$/i' => Attribute::CS_STRING,
            '/^aria-disabled$/i' => Attribute::CS_STRING,
            '/^aria-dropeffect$/i' => Attribute::CS_STRING,
            '/^aria-errormessage$/i' => Attribute::CS_STRING,
            '/^aria-flowto$/i' => Attribute::CS_STRING,
            '/^aria-grabbed$/i' => Attribute::CS_STRING,
            '/^aria-haspopup$/i' => Attribute::CS_STRING,
            '/^aria-hidden$/i' => Attribute::CS_STRING,
            '/^aria-invalid$/i' => Attribute::CS_STRING,
            '/^aria-label$/i' => Attribute::CS_STRING,
            '/^aria-labelledby$/i' => Attribute::CS_STRING,
            '/^aria-live$/i' => Attribute::CS_STRING,
            '/^aria-owns$/i' => Attribute::CS_STRING,
            '/^aria-relevant$/i' => Attribute::CS_STRING,
            '/^aria-roledescription$/i' => Attribute::CS_STRING,

            // ARIA widget attributes
            '/^aria-autocomplete$/i' => Attribute::CS_STRING,
            '/^aria-checked$/i' => Attribute::CS_STRING,
            '/^aria-expanded$/i' => Attribute::CS_STRING,
            '/^aria-level$/i' => Attribute::CS_STRING,
            '/^aria-modal$/i' => Attribute::CS_STRING,
            '/^aria-multiline$/i' => Attribute::CS_STRING,
            '/^aria-multiselectable$/i' => Attribute::CS_STRING,
            '/^aria-orientation$/i' => Attribute::CS_STRING,
            '/^aria-placeholder$/i' => Attribute::CS_STRING,
            '/^aria-pressed$/i' => Attribute::CS_STRING,
            '/^aria-readonly$/i' => Attribute::CS_STRING,
            '/^aria-required$/i' => Attribute::CS_STRING,
            '/^aria-selected$/i' => Attribute::CS_STRING,
            '/^aria-sort$/i' => Attribute::CS_STRING,
            '/^aria-valuemax$/i' => Attribute::CS_STRING,
            '/^aria-valuemin$/i' => Attribute::CS_STRING,
            '/^aria-valuenow$/i' => Attribute::CS_STRING,
            '/^aria-valuetext$/i' => Attribute::CS_STRING,

            // ARIA relationship attributes
            '/^aria-activedescendant$/i' => Attribute::CS_STRING,
            '/^aria-colcount$/i' => Attribute::CS_STRING,
            '/^aria-colindex$/i' => Attribute::CS_STRING,
            '/^aria-colspan$/i' => Attribute::CS_STRING,
            '/^aria-posinset$/i' => Attribute::CS_STRING,
            '/^aria-rowcount$/i' => Attribute::CS_STRING,
            '/^aria-rowindex$/i' => Attribute::CS_STRING,
            '/^aria-rowspan$/i' => Attribute::CS_STRING,
            '/^aria-setsize$/i' => Attribute::CS_STRING
        );
    }

    private function getAttributeParameters($key)
    {
        $allowedAttributes = $this->getAllowedAttributes();
        foreach ($allowedAttributes as $attrRegex => $valueType) {
            if (preg_match($attrRegex, $key) === 1) {
                return array(
                    'name' => $key,
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
    public function remove(LoggerInterface $logger)
    {
        $hasRemovableElements = $this->configuration->get('element-blacklist') != '';
        $hasRemovableTypes = $this->configuration->get('type-blacklist') != '';
        foreach ($this->children as $child) {
            // Check types.
            if ($hasRemovableTypes &&
                !$this->configuration->isAllowedType($child->getType())) {
                $logger->debug('Removing ' . $child);
                $this->removeChild($child);

                continue;
            }

            // Check elements.
            if ($hasRemovableElements &&
                $child instanceof self &&
                !$this->configuration->isAllowedElement($child->getName())) {
                $logger->debug('Removing ' . $child);
                $this->removeChild($child);

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
        foreach ($this->attributes as $attribute) {
            $output .= ' ' . (string) $attribute;
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

    public function getType()
    {
        return Token::ELEMENT;
    }

    public function __toString()
    {
        return '"' . $this->name . '" element';
    }
}
