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
class RadioButton extends Field
{
    const Input_Type = 'radio';

    /**
     * Attributes of the input element
     *
     * @return array
     */
    public function get_attributes_default( )
    {
        $default = array(
            'type'              => self::Input_Type,
            'class'             => 'input-radio',
            'class-label'       => 'radio',

            // Unique to this class
            'choices'           => [ 'unset' => 'unset' ],
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

        if ( ! isset( $post[ $name ] ) )
        {
            return null;
        }
        $raw = $post[ $name ];

        if ( empty( $raw ) )
        {
            if ( $this->is_required() )
            {
                return new Field_Error( $this, 'Value is required', $raw );
            }
            else
            {
                return null;
            }
        }
        
        $values = array_keys( $this->get_attribute( 'choices' ) );
        if ( in_array( $raw, $values, True ) )
        {
            return null;
        }

        $acceptable = implode("', '", $values);
        $error = "Selected value not valid ( '$raw' is not in '$acceptable' )";
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
        $name           = $this->get_attribute( 'name' );
        $value          = $this->get_attribute( 'value' );
        $choices        = $this->get_attribute( 'choices' );
        $css_label      = $this->get_attribute( 'class-label' );
        $required       = $this->is_required();

        if ( empty( $choices ) || ! is_array( $choices ))
        {
            return;
        }

        ?>
        <?php
        foreach ( $choices as $radio => $label )
        {
            $checked = $radio === $value;

            $label_text     = htmlspecialchars( $label );
            $label_before   = True;
            switch ( $this->get_attribute( 'text-position' ) )
            {
                case 'right':
                    if (! empty($label_text))
                    {
                        $label_text = '&nbsp;' . $label_text;
                    }
                    $label_before = False;
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
        ?>
        <label
            <?php HtmlHelper::print_attribute('class', $css_label) ?>
        >
            <?php
            if ( $label_before )
            {
                echo $label_text;
            }
            ?>
            <input
                <?php parent::render_attributes( [ 'value' ] ) ?>
                <?php HtmlHelper::print_attribute('value',   $radio) ?>
                <?php HtmlHelper::print_attribute('checked', $checked) ?>
            />
            <?php
            if ( ! $label_before )
            {
                echo $label_text;
            }
            ?>
        </label>
        <?php
        }
    }
}