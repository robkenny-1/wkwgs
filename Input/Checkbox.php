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
    const Default_Attributes    = array(
            'type'              => self::Input_Type,
            'value'             => 'yes',

            // Unique to this class
            'checked'           => 'no',
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
        $checked        = HtmlHelper::is_true( $this->get_attribute( 'checked' ) );

        $this->render_label_open();
        ?>
        <input
            <?php parent::render_input_attributes( ); ?>
            <?php HtmlHelper::print_attribute('checked', $checked) ?>
        >
        <?php
        $this->render_label_close();
  
    }
}