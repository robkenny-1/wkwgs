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
    const Class_Type        = 'checkbox';
    const Class_Attributes  = array(
        'type'              => self::Class_Type,
        'css-input'         => 'input-checkbox',
        'css-label'         => 'checkbox',
    );


    function __construct( $name )
    {
        parent::__construct( $name );
    }

    /**
     * Attributes of a checkbox
     *
     * @return array
     */
    public function get_attributes_default( )
    {
        $parent = parent::get_attributes_default();

        return array_merge($parent, self::Class_Attributes);
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
        $label_text     = htmlspecialchars( $this->get_attribute( 'label' )  );
        $css_input      = esc_attr( $this->get_attribute( 'css-input' )      );
        $css_label      = esc_attr( $this->get_attribute( 'css-label' )      );
        $css_input_span = esc_attr( $this->get_attribute( 'css-input-span' ) );

        $name           = $this->html_prefix( $name );
        $checked        = Field::is_true( $this->get_attribute( 'value' ) );
        $checked        = $checked ? 'checked="checked"' : '';

        ?>
        <span class="<?php echo $css_input_span ?>">
            <label class="<?php echo $css_label ?>">
                <input
                    type="<?php echo $type ?>"
                    class="<?php echo $css_input ?>"
                    name="<?php echo $name ?>"
                    id="<?php echo $name ?>"
                    value="yes"
                    <?php echo $checked ?>
                />&nbsp;<?php echo $label_text ?>
            </label>
        </span>
        <?php    
    }
}