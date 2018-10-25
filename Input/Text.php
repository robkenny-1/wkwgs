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
 * The text input Class
 *
 * @since 1.0.0
 */
class Text extends Field
{
    const Input_Type            = 'text';
    const Default_Attributes    = array(
            'type'              => self::Input_Type,
        );

    public function __construct( $attributes )
    {
        parent::__construct( $attributes );
        $this->merge_attributes_default( self::Default_Attributes );
    }

    /**
     * Verify status of input data
     *
     * @return null if no error or Field_Error
     */
    public function validate( $post )
    {
        $name = $this->get_name();

        if ( ! isset( $post[ $name ] ) )
        {
            return new Field_Error( $this, 'Value not in POST' );
        }

        $raw = $post[ $name ];

        if ( empty( $raw ) )
        {
            if ( $this->is_required() )
            {
                return new Field_Error( $this, 'Value is required', $raw );
            }
            return null;
        }

        return null;
    }

    /**
     * Extract object's value from post data
     *
     * @return input value
     */
    public function get_value( $post )
    {
        $name = $this->get_name();

        if ( $this->validate( $post ) != null )
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
    public function render_input( $exclude )
    {
        echo '<input ';
        parent::render_input_attributes( $exclude );
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
        $exclude = [];
        $this->render_label( [$this, 'render_input' ], [ $exclude ] );
    }
}