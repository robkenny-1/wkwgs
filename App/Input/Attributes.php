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

include_once ('Input.php');

/* ------------------------------------------------------------------------- */
/* Manage a collection of key/value pairs (aka HTML attributes) */
/* ------------------------------------------------------------------------- */
class Attributes implements IAttributeSecondary, IHtmlPrinter
{

    public function __construct(array $attributes, array $default = [], array $secondary = [])
    {
        $this->set_attributes($attributes, $default);
        $this->set_attributes_secondary($secondary);
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
        $this->invalidate_cache();

        $this->attributes = $attributes ?? [];
        $this->default = $default ?? [];
    }

    /**
     * Get the complete list of attributes, with default values set
     *
     * @return array, current attributes
     */
    public function get_attributes(): array
    {
        $this->update_cache();
        $attributes = $this->cached;

        // ->log_return( $attributes );
        return $attributes;
    }

    /**
     * Get the value of a single attribute of the field
     *
     * @return string
     */
    public function get_attribute(string $attribute)
    {
        $attribute = $this->get_attributes()[$attribute] ?? '';

        return $attribute;
    }

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute(string $attribute, $value)
    {
        $this->invalidate_cache();

        $this->attributes[$attribute] = $value;
    }

    /* --------------------------------------------------------------------- */
    /* IAttributeSecondary routines */
    /* --------------------------------------------------------------------- */

    /**
     * Define attributes that belong to secondary elements
     *
     * @param array $secondary
     *            non-associative array of attribute names
     *            that exist for the secondary elements
     * @return null
     */
    public function set_attributes_secondary(array $secondary)
    {
        $this->invalidate_cache();
        $this->secondary = array_values($secondary) ?? [];
    }

    /**
     * Get the list of defined secondary attributes
     *
     * @return array
     */
    public function get_attributes_secondary_names(): array
    {
        return $this->secondary;
    }

    /**
     * Get the secondary attributes for this input object
     *
     * @return array of the secondary values
     */
    public function get_attributes_secondary(): array
    {
        $this->update_cache();
        $attributes = $this->cached_secondary;

        // ->log_return( $attributes );
        return $attributes;
    }

    /**
     * Get the value of a single secondary attribute
     *
     * @return mixed, value of $attribute. Empty string if unset
     */
    public function get_attribute_secondary(string $attribute)
    {
        $attribute = $this->get_attributes_secondary()[$attribute] ?? '';

        return $attribute;
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
        $attributes = $this->get_attributes();

        $html = '';

        foreach ($attributes as $attribute => $value)
        {
            $attribute_html = Helper::get_html_attribute($attribute, $value);
            if (! empty($attribute_html))
            {
                $html .= ' ';
                $html .= $attribute_html;
            }
        }

        return $html;
    }

    /* --------------------------------------------------------------------- */
    /* Implementation */
    /* --------------------------------------------------------------------- */
    /*
     * Current attributes
     */
    protected $attributes;

    protected $default;

    protected $secondary;

    protected $cached;

    protected $cached_secondary;

    protected function invalidate_cache()
    {
        $this->cached = null;
        $this->cached_secondary = null;
    }

    protected function update_cache()
    {
        if (is_null($this->cached) || is_null($this->cached_secondary))
        {
            $secondary = [];
            $attributes = array_merge($this->default, $this->attributes);

            foreach ($this->secondary as $secondary_value)
            {
                if (Helper::ends_with($secondary_value, '-'))
                {
                    $secondary_name = substr($secondary_value, 0, - 1);
                    $secondary[$secondary_name] = Attributes::move_values($secondary_value, $attributes);
                }
                else
                {
                    if (isset($attributes[$secondary_value]))
                    {
                        // move $secondary_value from $attributes to $secondary
                        $secondary[$secondary_value] = $attributes[$secondary_value];
                        unset($attributes[$secondary_value]);
                    }
                }
            }

            $this->cached = $attributes;
            $this->cached_secondary = $secondary;
        }
    }

    /*
     * If $secondary_value ends with a -, move all matching
     * to a label array in $secondary
     * Example:
     * $secondary_value = 'label-';
     * $attributes = [ 'label-class' => 'aaa', 'style' => 'bbb', 'label-id' => 'ccc' ];
     * returns
     * [ 'class' => 'aaa', 'id' => 'ccc' ]
     * $attributes = [ 'style' => 'bbb' ];
     */
    public static function move_values(string $secondary_value, array & $attributes): array
    {

        // Build the PCRE pattern
        $delim = '#';
        $pattern = $delim . '^' . preg_quote($secondary_value, $delim) . '(.*)$' . $delim;

        $moved = [];

        foreach ($attributes as $rr => $rr_value)
        {
            $matches = [];

            if (preg_match($pattern, $rr, $matches) === 1)
            {
                $new_key = $matches[1];

                $moved[$new_key] = $rr_value;
                unset($attributes[$rr]);
            }
        }

        return $moved;
    }

    /**
     * Get and remove the named attribute from the array
     *
     * @return mixed Value of the attribute or $default
     */
    public static function get_attribute_and_remove(string $attribute, array & $attributes, string $default = '')
    {
        $value = $default;

        if (isset($attributes[$attribute]))
        {
            $value = $attributes[$attribute];
            unset($attributes[$attribute]);
        }

        return $value;
    }
}
