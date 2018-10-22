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

    function __construct( $name )
    {
        parent::__construct( $name );
    }

    /**
     * Attributes of the input element
     *
     * @return array
     */
    public function get_attributes_default( )
    {
        $default = array(
            'type'              => self::Input_Type,
            'value'             => '', // when checked value === selection-value
            'selection-value'   => 'yes',
            'text-position'     => 'right',
            'css-input'         => 'input-checkbox',
            'css-label'         => 'checkbox',
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
        \Wkwgs_Logger::log_function( 'Checkbox::validate');
        \Wkwgs_Logger::log_var( '$this->get_name()', $this->get_name() );
        \Wkwgs_Logger::log_var( '$post', $post );

        $is_valid = False;

        if ( isset( $post[ $this->get_name() ] ) )
        {
            $raw = $post[ $this->get_name() ];

            \Wkwgs_Logger::log_var( '$raw', $raw );
            \Wkwgs_Logger::log_var( '$this->get_attribute( "value" )', $this->get_attribute( 'selection-value' ) );

            $is_valid = $raw === $this->get_attribute( 'selection-value' );
        }

        \Wkwgs_Logger::log_var( 'return $is_valid', $is_valid );
        return $is_valid;
    }

    /**
     * Extract object's value from post data
     *
     * @return input value
     */
    public function get_value( $post )
    {
        \Wkwgs_Logger::log_function( 'Checkbox::get_value');

        $cleansed = '';

        if ( $this->validate( $post ) )
        {
            $raw = $post[ $this->get_name() ];

            // No cleansing necessary
            $cleansed = $raw;
        }

        \Wkwgs_Logger::log_var( 'return $cleansed', $cleansed );
        return $cleansed;
    }

    /**
     * Render the field in the frontend, this spits out the necessary HTML
     *
     * @return void
     */
    public function render( )
    {
        $type           = $this->get_attribute( 'type' );
        $name           = $this->get_attribute( 'name' );
        $id             = $this->get_attribute( 'id' );
        $value          = $this->get_attribute( 'selection-value' );
        $css_input      = $this->get_attribute( 'css-input' );
        $css_label      = $this->get_attribute( 'css-label' );
        $css_input_span = $this->get_attribute( 'css-input-span' );
        $checked        = $this->get_attribute( 'selection-value' ) === $this->get_attribute( 'value' );
        $label_text     = htmlspecialchars( $this->get_attribute( 'label' )  );
        $text_pos       = $this->get_attribute( 'text-position' );

        /*
        \Wkwgs_Logger::log_function( 'Checkbox::render');
        \Wkwgs_Logger::log_var( '$type', $type );
        \Wkwgs_Logger::log_var( '$name', $name );
        \Wkwgs_Logger::log_var( '$label_text', $label_text );
        \Wkwgs_Logger::log_var( '$this->get_attribute( "selection-value" )', $this->get_attribute( 'selection-value' ) );
        \Wkwgs_Logger::log_var( '$checked', $checked );
        \Wkwgs_Logger::log_var( 'get_attributes', $this->get_attributes() );
        */

        ?>
        <span
            <?php Field::html_print_attribute('class',      $css_input_span) ?>
            <label
                <?php Field::html_print_attribute('class', $css_label) ?>
            >
                <?php
                if ( $text_pos === 'top' )
                {
                    echo $label_text . '</br>';
                }
                else if ( $text_pos === 'left' )
                {
                    echo $label_text . '&nbsp;';
                }
                ?>
                <input
                    <?php Field::html_print_attribute('type',       $type) ?>
                    <?php Field::html_print_attribute('class',      $css_input) ?>
                    <?php Field::html_print_attribute('name',       $name) ?>
                    <?php Field::html_print_attribute('id',         $id) ?>
                    <?php Field::html_print_attribute('value',      $value) ?>
                    <?php Field::html_print_attribute('checked',    $checked) ?>
                />
                <?php
                if ( $text_pos === 'bottom' )
                {
                    echo '</br>' . $label_text;
                }
                else if ( $text_pos === 'right' )
                {
                    echo '&nbsp;' . $label_text;
                }
                ?>
             </label>
        </span>
        <?php    
    }
}