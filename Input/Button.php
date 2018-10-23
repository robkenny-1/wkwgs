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
            'class'             => 'button',
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

        if ( ! isset( $post[ $name ] ) || $this->validate( $post ) != null )
        {
            return '';
        }
        $raw = $post[ $name ];

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
        $type           = $this->get_attribute( 'button-type' );
        $name           = $this->get_attribute( 'name' );
        $id             = $this->get_attribute( 'id' );
        $value          = $this->get_attribute( 'value' );
        $css_input      = $this->get_attribute( 'class' );

        //\Wkwgs_Logger::log_function( 'Button->render');
        //\Wkwgs_Logger::log_var( '$type', $type );
        //\Wkwgs_Logger::log_var( '$name', $name );
        //\Wkwgs_Logger::log_var( '$value', $value );

        ?>
        <button
            <?php HtmlHelper::print_attribute('type',       $type) ?>
            <?php HtmlHelper::print_attribute('class',      $css_input) ?>
            <?php HtmlHelper::print_attribute('name',       $name) ?>
            <?php HtmlHelper::print_attribute('id',         $id) ?>
        ><?php echo $value ?></button>
        <?php    
    }
}