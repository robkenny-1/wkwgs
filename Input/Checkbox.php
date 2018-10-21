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
            'value'             => 'yes',
            'selected'          => '',
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

        $is_valid = False;

        if ( isset( $post[ $this->get_name() ] ) )
        {
            $raw = $post[ $this->get_name() ];

            \Wkwgs_Logger::log_var( '$raw', $raw );
            \Wkwgs_Logger::log_var( '$this->get_attribute( "value" )', $this->get_attribute( 'value' ) );

            $is_valid = $raw === $this->get_attribute( 'value' );
        }

        return $is_valid;
    }

    /**
     * Extract object's value from post data
     *
     * @return input value
     */
    public function get_value( $post )
    {
        $cleansed = '';

        if ( $this->validate( $post ) )
        {
            $raw = $post[ $this->get_name() ];

            // No cleansing necessary
            $cleansed = $raw;
        }

        return $cleansed;
    }

    /**
     * Render the field in the frontend, this spits out the necessary HTML
     *
     * @return void
     */
    public function render( )
    {
        $type           = esc_attr( $this->get_attribute( 'type' )           );
        $name           = esc_attr( $this->get_attribute( 'name' )           );
        $value          = esc_attr( $this->get_attribute( 'value' )          );
        $label_text     = htmlspecialchars( $this->get_attribute( 'label' )  );
        $css_input      = esc_attr( $this->get_attribute( 'css-input' )      );
        $css_label      = esc_attr( $this->get_attribute( 'css-label' )      );
        $css_input_span = esc_attr( $this->get_attribute( 'css-input-span' ) );

        $checked        = $this->get_attribute( 'selected' ) === $this->get_attribute( 'value' );

        /*
        \Wkwgs_Logger::log_function( 'Checkbox::render');
        \Wkwgs_Logger::log_var( '$type', $type );
        \Wkwgs_Logger::log_var( '$name', $name );
        \Wkwgs_Logger::log_var( '$label_text', $label_text );
        \Wkwgs_Logger::log_var( '$this->get_attribute( "value" )', $this->get_attribute( 'value' ) );
        \Wkwgs_Logger::log_var( '$checked', $checked );
        \Wkwgs_Logger::log_var( 'get_attributes', $this->get_attributes() );
        */
        $checked        = $checked ? 'checked="checked"' : '';

        ?>
        <span class="<?php echo $css_input_span ?>">
            <label class="<?php echo $css_label ?>">
                <input
                    type="<?php echo $type ?>"
                    <?php if ( ! empty( $id ) ) { echo 'id="' . $id . '"'; } ?>
                    name="<?php echo $name ?>"
                    value="<?php echo $value ?>"
                    class="<?php echo $css_input ?>"
                    <?php echo $checked ?>/>&nbsp;<?php echo $label_text ?></label>
        </span>
        <?php    
    }
}