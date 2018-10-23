<?php
/*
    Input Copyright (C) 2018 Rob Kenny

    WordPress Plugin Template is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    WordPress Plugin Template is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Contact Form to Database Extension.
    If not, see http://www.gnu.org/licenses/gpl-3.0.html
*/

namespace Input;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

include_once('Constants.php');
include_once('Field.php');

/**
 * The checkbox input class
 *
 * @since 1.0.0
 */
class Checkbox extends Field
{
    const Input_Type = 'checkbox';

    /**
     * Attributes of the input element
     *
     * @return array
     */
    public function get_attributes_default( )
    {
        $default = array(
            'type'              => self::Input_Type,
            'value'             => 'yes',
            'css-input'         => 'input-checkbox',
            'css-label'         => 'checkbox',

            // Unique to this class
            'checked'           => 'no',
        );

        $parent = parent::get_attributes_default();

        return array_merge($parent, $default);
    }

    /**
     * Verify data is conforms to an email address
     *
     * @return null if no error or Field_Error
     */
    public function validate( $post )
    {
        $name = $this->get_name();

        // Unselected checkbox are not present in POST
        if ( ! isset( $post[ $name ] ) )
        {
            $raw = '';
        }
        else
        {
            $raw = $post[ $name ];
        }

        if ( empty( $raw ) )
        {
            if ( $this->is_required() )
            {
                return new Field_Error( $this, 'Value is required', $raw );
            }
            return null;
        }

        $value = $this->get_attribute( 'value' );
        if ( $raw === $value)
        {
            return null;
        }

        $error = "Selected value not valid ( '$raw', expected '$value' )";
        return new Field_Error( $this, $error, $value );
    }


    /**
     * Extract object's value from post data
     *
     * @return input value
     */
    public function get_value( $post )
    {
        $name = $this->get_name();

        if ( ! isset( $post[ $name ] ) || $this->validate( $post ) != null )
        {
            return '';
        }
        $raw = $post[ $name ];

        // No cleansing necessary
        $cleansed = $raw;

        return $cleansed;
    }

    /**
     * Render the field in the frontend, this spits out the necessary HTML
     *
     * @return void
     */
    public function render( )
    {
        $css_label      = $this->get_attribute( 'css-label' );
        $css_input_span = $this->get_attribute( 'css-input-span' );
        $label_text     = $this->get_attribute( 'label' );
        $text_pos       = $this->get_attribute( 'text-position' );
        $css_label      = $this->get_attribute( 'css-label' );
        $css_input_span = $this->get_attribute( 'css-input-span' );
        $checked        = Field::is_true( $this->get_attribute( 'checked' ) );
        $required       = $this->is_required();

        switch ( $this->get_attribute( 'text-position' ) )
        {
            case 'bottom':
                if (! empty($label_text))
                {
                    $label_text = '<br>' . $label_text;
                }
                $label_before = False;
                break;

            case 'right':
                if (! empty($label_text))
                {
                    $label_text = '&nbsp;' . $label_text;
                }
                $label_before = False;
                break;

            case 'top':
                if (! empty($label_text))
                {
                    $label_text = $label_text . '<br>';
                }
                $label_before = True;
                break;

            case 'left':
            default:
                if (! empty($label_text))
                {
                    $label_text = $label_text . '&nbsp;';
                }
                $label_before = True;
                break;
        }

        if ( $required )
        {
            $label_text .= '<abbr class="required" title="required">&nbsp;*</abbr>';
        }

        ?>
        <span
            <?php Field::html_print_attribute('class', $css_input_span) ?>
        >
            <label
                <?php Field::html_print_attribute('class', $css_label) ?>
            >
                <?php
                if ( $label_before )
                {
                    echo $label_text;
                }
                ?>
                <input
                    <?php parent::render_attributes( ) ?>
                    <?php Field::html_print_attribute('checked', $checked) ?>
                />
                <?php
                if ( ! $label_before )
                {
                    echo $label_text;
                }
                ?>
             </label>
        </span>
        <?php    
    }
}