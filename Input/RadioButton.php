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
            'css-input'         => 'input-radio',
            'css-label'         => 'radio',
            'selected'          => '',
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
        $raw = $post[ $this->get_name() ];

        return in_array( $raw, $this->get_attribute( 'choices' ) );
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

        return $raw;
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
        $value          = $this->get_attribute( 'value' );
        $css_input      = $this->get_attribute( 'css-input' );
        $css_label      = $this->get_attribute( 'css-label' );
        $css_input_span = $this->get_attribute( 'css-input-span' );
        $label_text     = htmlspecialchars( $this->get_attribute( 'label' )  );

        $value          = $this->get_attribute( 'value' );
        $options        = $this->get_attribute( 'choices' );

        if ( ! empty( $options ) )
        {
            ?>
            <span class="<?php echo $css_input_span ?>">
                <label
                    <?php Field::html_print_attribute('for',        $name) ?>
                    <?php Field::html_print_attribute('class',      $css_label) ?>
                ><?php echo $label_text ?></label>
                <?php
                foreach ( $options as $option => $option_text )
                {
                    $option_text    = htmlspecialchars( $option_text );
                    $is_selected    = $option === $value;

                    ?>
                    <input
                        <?php Field::html_print_attribute('class',      $css_input) ?>
                        <?php Field::html_print_attribute('type',       $type) ?>
                        <?php Field::html_print_attribute('name',       $name) ?>
                        <?php Field::html_print_attribute('id',         $name . '_' . $option) ?>
                        <?php Field::html_print_attribute('value',      $value) ?>
                        <?php Field::html_print_attribute('checked',    $is_selected) ?>
                    />&nbsp;<?php echo $option_text ?>

                    <?php
                }
            ?></span><?php
        }
    }
}