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
    const Input_Type = 'text';

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
            'label'             => 'unset',
            'css-input'         => 'input-text',
            'css-label'         => '',
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

        // Should we always perform some cleansing or leave it up to the caller?
        return $raw;
    }

    /**
     * Render the field in the frontend, this spits out the necessary HTML
     *
     * @return void
     */
    public function render( )
    {
        \Wkwgs_Logger::log_function( 'Text::render');
        \Wkwgs_Logger::log_var( '$this', $this );

        $type           = $this->get_attribute( 'type' );
        $name           = $this->get_attribute( 'name' );
        $id             = $this->get_attribute( 'id' );
        $value          = $this->get_attribute( 'value' );
        $css_input      = $this->get_attribute( 'css-input' );
        $css_label      = $this->get_attribute( 'css-label' );
        $css_input_span = $this->get_attribute( 'css-input-span' );
        $placeholder    = $this->get_attribute( 'placeholder' );
        $value          = $this->get_attribute( 'value' );
        $label_text     = htmlspecialchars( $this->get_attribute( 'label' )  );
        $label_before   = True;

        if ( $this->is_required() && empty($label_text) )
        {
            $label_text .= '<abbr class="required" title="required">&nbsp;*</abbr>';
        }
        \Wkwgs_Logger::log_var( '$label_text', $label_text );

        switch ( $this->get_attribute( 'text-position' ) )
        {
            case 'bottom':
                if (! empty($label_text))
                {
                    $label_text = '</br>' . $label_text;
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
                    $label_text = $label_text . '</br>';
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
        \Wkwgs_Logger::log_var( '$label_text', $label_text );

        ?>
        <span
            <?php Field::html_print_attribute('class',      $css_input_span) ?>
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
                    <?php Field::html_print_attribute('type',           $type) ?>
                    <?php Field::html_print_attribute('class',          $css_input) ?>
                    <?php Field::html_print_attribute('name',           $name) ?>
                    <?php Field::html_print_attribute('id',             $id) ?>
                    <?php Field::html_print_attribute('value',          $value) ?>
                    <?php Field::html_print_attribute('placeholder',    $placeholder) ?>
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