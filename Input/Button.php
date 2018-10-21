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
class Button extends Field
{
    const Input_Type = 'button';

    function __construct( $name )
    {
        parent::__construct( $name );
    }

    /**
     * Attributes of this input element
     *
     * @return array
     */
    public function get_attributes_default( )
    {
        $default = array(
            'type'              => self::Input_Type,
            'button-type'       => 'submit',
            'css-input'         => 'button',
        );

        $parent = parent::get_attributes_default();

        return array_merge($parent, $default);
    }

    /**
     * Verify status of input data
     *
     * @return True if value meets criteria
     */
    public function validate( $post )
    {
        if ( ! isset( $post[ $this->get_name() ] ) )
        {
            return False;
        }
        return True;
    }

    /**
     * Extract object's value from post data
     *
     * @return input value
     */
    public function get_value( $post )
    {
        if ( ! $this->validate( $post ) )
        {
            return '';
        }
        $raw = $post[ $this->get_name() ];

        // The button should only return 'value'
        if ( $raw === $this->get_attribute( 'value' ) )
        {
            return $raw;
        }
        return '';
    }

    /**
     * Render the field in the frontend, this spits out the necessary HTML
     *
     * @return void
     */
    public function render( )
    {
        $type           = esc_attr( $this->get_attribute( 'button-type' )    );
        $name           = esc_attr( $this->get_attribute( 'name' )           );
        $css_input      = esc_attr( $this->get_attribute( 'css-input' )      );
        $value          = esc_attr( $this->get_attribute( 'value' )          );

        \Wkwgs_Logger::log_function( 'Button->render');
        \Wkwgs_Logger::log_var( '$type', $type );
        \Wkwgs_Logger::log_var( '$name', $name );
        \Wkwgs_Logger::log_var( '$value', $value );

        ?>
        <button
            type='<?php echo $type ?>'
            name='<?php echo $name ?>'
            class='<?php echo $css_input ?>' >
            <?php echo $value ?>
        </button>
        <?php    
    }
}