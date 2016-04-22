<?php

namespace Groundskeeper\Tokens;

class Element extends AbstractToken
{
    /** @var array */
    private $attributes;

    /** @var array[Token] */
    private $children;

    /** @var string */
    private $name;

    /**
     * Constructor
     */
    public function __construct(Token $parent = null, $name = null, array $attributes = array())
    {
        parent::__construct(Token::ELEMENT, $parent);

        $this->attributes = $attributes;
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

    public function addAttribute($key, $value)
    {
        $this->attributes[$key] = $value;

        return $this;
    }

    public function removeAttribute($key)
    {
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

        $this->name = $name;

        return $this;
    }

    public function toString($prefix = '', $suffix = '')
    {
        $output = $prefix . '<' . $this->name;
        foreach ($this->attributes as $key => $value) {
            $output .= ' ' . $key;
            if (is_string($value)) {
                $output .= '="' . $value . '"';
            }
        }

        if (empty($this->children)) {
            $output .= '/>';
        } else {
            $output .= '>';
        }

        return $output . $suffix;
    }
}
