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
 * The Label class only displays text
 *
 * @since 1.0.0
 */
class Label extends Field
{
    const Input_Type = 'label';

    /**
     * Attributes of the input element
     *
     * @return array
     */
    public function get_attributes_default( )
    {
        $default = array(
            'type'              => self::Input_Type,
            'class'             => 'input-text',
        );

        $parent = parent::get_attributes_default();

        return array_merge($parent, $default);
    }

    /**
     * Verify status of input data
     *
     * @return null if no error or Field_Error
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
        return '';
    }

    /**
     * Render the field in the frontend, this spits out the necessary HTML
     *
     * @return void
     */
    public function render( )
    {
        \Wkwgs_Logger::log_function( 'Label::render');
        $css_label      = $this->get_attribute( 'class-label' );
        $label_text     = htmlspecialchars( $this->get_attribute( 'label' )  );
        \Wkwgs_Logger::log_var( '$label_text', $label_text );

        ?>
        <label
            <?php Field::html_print_attribute('class', $css_label) ?>
        ><?php echo $label_text; ?></label>
        <?php    
    }
}