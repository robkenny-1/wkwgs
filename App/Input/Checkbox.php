<?php

/*
 * Input Copyright (C) 2018 Rob Kenny
 *
 * WordPress Plugin Template is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * WordPress Plugin Template is distributed in the hope that it will be useful,
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

/**
 * The checkbox input class
 *
 * @since 1.0.0
 */
class Checkbox extends InputElement
{
    const Tag_Type = 'input';
    const Attributes_Default = [
        'type' => 'checkbox',
        'checked' => FALSE,
        'value' => 'TRUE',
    ];
    const Attributes_Secondary = [
        'label',
    ];

    public function __construct($desc)
    {
        if (gettype($desc) !== 'array')
        {
            return;
        }

        $desc['tag'] = self::Tag_Type;
        parent::__construct($desc);
    }

    /* --------------------------------------------------------------------- */
    /* IAttributeProvider routines */
    /* --------------------------------------------------------------------- */
    public function define_attribute_default(): array
    {
        $parent = parent::define_attribute_default();
        return array_merge($parent, self::Attributes_Default);
    }

    /* --------------------------------------------------------------------- */
    /* IAttributeSecondaryProvider routines */
    /* --------------------------------------------------------------------- */
    public function define_attribute_secondary(): array
    {
        $parent = parent::define_attribute_secondary();
        return array_merge($parent, self::Attributes_Secondary);
    }

    /* --------------------------------------------------------------------- */
    /* IHtmlInput routines */
    /* --------------------------------------------------------------------- */

    /**
     * Set the contents of the input element
     * Some input elements, such as the checkbox, do not store their current
     * contents in the value attribute.
     * This routine, given the value returned
     * by get_value(), sets the appropriate attribute.
     *
     * @param mixed $value
     *            New
     *            value of the input element
     */
    public function set_value($value)
    {
        $checked = $value === $this->get_attribute('value');

        $this->set_attribute('checked', $checked);
    }

    /* --------------------------------------------------------------------- */
    /* InputElement routines */
    /* --------------------------------------------------------------------- */

    /**
     * Validate data for a checkbox
     *
     * @return array of HtmlValidateError, empty if no errors
     */
    public function validate_post(string $name, array $post): array
    {
        $ve = [];

        // Perform data validation

        $raw = $post[$name] ?? '';
        $value = $this->get_attribute('value');

        if (empty($value))
        {
            $ve[] = new HtmlValidateError('checkbox definition error: value must not be empty', $name, $this);
        }
        else if (! empty($raw) && $value !== $raw)
        {
            $ve[] = new HtmlValidateError('$post value does not match expected', $name, $this);
        }

        return $ve;
    }

    public function cleanse_data($raw)
    {
        $cleansed = null;

        // No cleansing necessary?
        $cleansed = $raw;

        return $cleansed;
    }
}
