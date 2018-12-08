<?php

/*
 * Input Copyright (C) 2018 Rob Kenny
 *
 * Input is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Input is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Contact Form to Database Extension.
 * If not, see http://www.gnu.org/licenses/gpl-3.0.html
 */
namespace Wkwgs\Input;

// Exit if accessed directly
defined('ABSPATH') || exit();

include_once (__DIR__ . '/Input.php');

/* ------------------------------------------------------------------------- */
/* Classes */
/* ------------------------------------------------------------------------- */
class Element extends ElementList implements IHtmlElement
{

    // string
    protected $tag;

    // [ string ]
    protected $attributes;

    // ElementList
    public function __construct($desc)
    {
        if (gettype($desc) === 'string')
        {
            $tag = $desc;
            $attributes = [];
            $children = [];
        }
        else
        {
            $tag = $desc['tag'] ?? '';
            $attributes = $desc['attributes'] ?? [];
            $children = $desc['contents'] ?? [];
        }

        if (empty($tag))
        {
            $msg = '$tag is empty';
            throw new \Exception($msg);
        }

        $this->tag = $tag;
        $default = $this->define_attribute_default();
        $compound = $this->define_attribute_secondary();
        $this->attributes = new Attributes($attributes, $default, $compound);
        parent::__construct($children);
    }

    /* --------------------------------------------------------------------- */
    /* IHtmlElement routines */
    /* --------------------------------------------------------------------- */
    public function get_tag(): string
    {
        return $this->tag;
    }

    /* --------------------------------------------------------------------- */
    /* IHtmlPrinter routines */
    /* --------------------------------------------------------------------- */

    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html(): string
    {
        $tag = $this->tag;
        $tag = htmlspecialchars($tag);

        $html = '';

        $is_void = Helper::is_void_element($this->tag);

        $html .= "<$tag";
        $html .= $this->attributes->get_html();
        $html .= '>';
        if (! $is_void)
        {
            $html .= parent::get_html();
            $html .= "</$tag>";
        }

        return $html;
    }

    /* --------------------------------------------------------------------- */
    /* IAttribute routines */
    /* --------------------------------------------------------------------- */

    /**
     * Set the attributes of both types
     *
     * @param array $attributes
     *            associative array of attribute name/value
     * @return null
     */
    public function set_attributes(array $attributes, array $default = [])
    {
        $this->attributes->set_attributes($attributes, $default);
    }

    /**
     * Get the attributes that are not in $compound
     *
     * @return array, current attributes
     */
    public function get_attributes(): array
    {
        return $this->attributes->get_attributes();
    }

    /**
     * Get the value of a single attribute
     *
     * @return mixed, value of $attribute. Empty string if unset
     */
    public function get_attribute(string $attribute)
    {
        return $this->attributes->get_attribute($attribute);
    }

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute(string $attribute, $value)
    {
        $this->attributes->set_attribute($attribute, $value);
    }

    /* --------------------------------------------------------------------- */
    /* IAttributeSecondary routines */
    /* --------------------------------------------------------------------- */

    /**
     * Define attributes that belong to compound elements
     *
     * @param array $compound
     *            non-associative array of attribute names
     *            that exist for the compound elements
     * @return null
     */
    public function set_attributes_secondary(array $compound)
    {
        $this->attributes->set_attributes_secondary($compound);
    }

    /**
     * Get the list of defined secondary attributes
     *
     * @return array
     */
    public function get_attributes_secondary_names(): array
    {
        return $this->attributes->get_attributes_secondary_names();
    }

    /**
     * Get the attributes that are in $compound
     *
     * @return array indexed array of the alternate values
     */
    public function get_attributes_secondary(): array
    {
        return $this->attributes->get_attributes_secondary();
    }

    /**
     * Get the value of a single alternate attribute
     *
     * @return mixed, value of $attribute. Empty string if unset
     */
    public function get_attribute_secondary(string $attribute)
    {
        return $this->attributes->get_attribute_secondary($attribute);
    }

    /* --------------------------------------------------------------------- */
    /* IAttributeProvider routines */
    /* --------------------------------------------------------------------- */
    public function define_attribute_default(): array
    {
        return [];
    }

    /* --------------------------------------------------------------------- */
    /* IAttributeSecondaryProvider routines */
    /* --------------------------------------------------------------------- */
    public function define_attribute_secondary(): array
    {
        return [];
    }

    /* --------------------------------------------------------------------- */
    /* Helper routines for HTML */
    /* --------------------------------------------------------------------- */

    /**
     * Echo this objects HTML string to output
     *
     * @return null
     */
    public function render()
    {
        $html = $this->get_html();
        echo $html;
    }
}
