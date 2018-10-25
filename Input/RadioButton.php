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
    const Input_Type            = 'radio';
    const Default_Attributes    = array(
            'type'              => self::Input_Type,
        
            // Unique to this class
            'choices'           => [ 'unset' => 'unset' ],
);

    public function __construct( $attributes )
    {
        parent::__construct( $attributes );
        $this->merge_attributes_default( self::Default_Attributes );
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
     * Render the <input> element
     *
     */
    public function render_input( $exclude, $radio, $checked )
    {
        echo '<input ';
        parent::render_input_attributes( $exclude );
        HtmlHelper::print_attribute('value',   $radio );
        HtmlHelper::print_attribute('checked', $checked );
        echo '/>';
    }

    /**
     * Render the field in the frontend, this spits out the necessary HTML
     * Expected output
     * <label>Label Text<input type='text' /></label>
     *
     * @return void
     */
    public function render( )
    {
        $value          = $this->get_attribute( 'value' );
        $choices        = $this->get_attribute( 'choices' );
        $label          = $this->get_attribute( 'label'        );
        $tooltip        = $this->get_attribute( 'data-tooltip' );
        $css_label      = $this->get_attribute( 'css-label'    );
        $exclude        = [ 'value' ];

        if ( empty( $choices ) || ! is_array( $choices ))
        {
            return;
        }

        foreach ( $choices as $radio => $label )
        {
            $checked = $radio === $value;

            $params = [ $exclude, $radio, $checked ];
            $this->render_label_explicit(
                [$this, 'render_input'],
                $params,
                $label,
                $tooltip,
                $css_label,
                False // Don't annotate radio button's label with * (required)
            );
        }
    }
}