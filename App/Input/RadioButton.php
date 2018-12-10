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

class RadioButton extends InputElement
{
    const Tag_Type = 'input';
    const Attributes_Default = [
        'type' => 'radio',
    ];
    const Attributes_Secondary = [
        // 'label-', // defined in parent class
        // 'container-', // defined in parent class
        'choices',
        'selected',

        // At runtime each choice will be added:
        // 'choices1-'
        // 'choices2-'
        // etc...
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
     * This routine, given the value returned by get_value(),
     * sets the appropriate attribute.
     *
     * @param mixed $value
     *            New
     *            value of the input element
     */
    public function set_value($value)
    {
        $choices = $this->get_attribute('choices');

        if (in_array($value, $choices))
        {
            $this->set_attribute('selected', $value);
        }
    }

    /* --------------------------------------------------------------------- */
    /* InputElement routines */
    /* --------------------------------------------------------------------- */

    /**
     * Validate data for a radio button
     *
     * @return array of HtmlValidateError, empty if no errors
     */
    public function validate_post(string $name, array $post): array
    {
        $ve = [];

        // Perform data validation

        $raw = $post[$name] ?? '';
        $choices = $this->get_attribute_secondary('choices');
        $choice_keys = array_keys($choices);

        if (empty($choice_keys))
        {
            $ve[] = new HtmlValidateError('RadioButton definition error: choices must not be empty', $name, $this);
        }
        else if (! empty($raw) && ! in_array($raw, $choice_keys, TRUE))
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

    /**
     * Get the HTML for the core (<input>) object
     * Since a radio button is actually multiple <input type='radio'> elements
     * we override InputElement::get_html_core to provide the HTML
     * for all elements
     *
     * @return string HTML of the <input> element
     */
    public function get_html_core(): string
    {
        $html = '';

        $choices = $this->get_attribute_secondary('choices');

        if (empty($choices))
        {
            // Render the simple (single) RadioButton <input type='radio'> element
            $html .= parent::get_html_core();
        }
        else
        {
            // Define each radio selection as a secondary attribute
            $this->set_secondary_attribute_for_radiobuttons($choices);

            $attributes = $this->get_attributes();
            $secondary = $this->get_attributes_secondary();

            // For each radio button defined in choices:
            // create a single RadioButton to render
            foreach ($choices as $value)
            {
                $rb_attributes = $attributes;
                if (isset($secondary[$value]))
                {
                    $rb_attributes = array_merge($rb_attributes, $secondary[$value]);
                }
                // Force the radio button's value to match value specified in choices
                $rb_attributes['value'] = $value;

                $rb = new RadioButton([
                    'attributes' => $rb_attributes,
                ]);

                $html .= $rb->get_html();
            }
        }

        return $html;
    }

    /* --------------------------------------------------------------------- */
    /* InputElement routines */
    /* --------------------------------------------------------------------- */

    /**
     * Get a list of radio button names that are not allowed,
     * as these values would conflict with the attribute extraction
     * example of forbidden names: label, or container
     *
     * @return array
     */
    protected function get_forbidden_secondary_names(): array
    {
        $forbidden = [];

        foreach ($this->define_attribute_secondary() as $sec)
        {
            if (Helper::ends_with($sec, '-'))
            {
                $forbidden[] = $sec;
            }
        }
        return $forbidden;
    }

    /**
     * Ensure that $name will not conflict with the resource extraction routines.
     *
     * @param string $name
     * @throws \Exception
     */
    protected function verify_valid_secondary_name(string $name)
    {
        if (Helper::ends_with($name, '-') || in_array($name, self::get_forbidden_secondary_names()))
        {
            $msg = self::class . ': cannot use "' . $name . '" for the name attribute.';
            throw new \Exception($msg);
        }
    }

    /**
     * To enable attributes to be specified for each individual radio button
     * we use the button's value a the identity of a resource group
     *
     * @param array $choices
     * @throws \Exception
     */
    protected function set_secondary_attribute_for_radiobuttons(array $choices): void
    {
        $all_secondary = $this->get_attributes_secondary_names();

        foreach ($choices as $value)
        {
            $this->verify_valid_secondary_name($value);

            if (! isset($all_secondary[$value]))
            {
                $all_secondary[] = $value . '-';
            }
        }
        $this->set_attributes_secondary($all_secondary);
    }
}
