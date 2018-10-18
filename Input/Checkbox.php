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
 * The Field Class
 *
 * @since 1.0.0
 */
class Checkbox extends Field
{
    const Class_Type        = 'checkbox';
    const Class_Attributes  = array(
        'type'              => Checkbox::Class_Type,
        'css-field'         => 'input-checkbox',
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
        $name           = $this->html_prefix( $this->get_attribute( 'name' ) );
        $label_text     = $this->get_attribute( 'label' );
        $checked        = $this->get_attribute( 'value' ) === 'yes' || $this->get_attribute( 'value' ) === '1' ? True : False;
        $checked        = $checked ? 'checked="checked"' : '';
        $css_input      = $this->get_attribute( 'css-field' );
        $css_label      = $this->get_attribute( 'css-label' );

        ?>
        <label class="<?php echo $css_label ?>">
            <input type="<?php echo $this->get_attribute( 'type' ) ?>"
                   class="<?php echo $css_input ?>"
                   name="<?php echo $name; ?>"
                   id="<?php echo $name; ?>"
                   value="yes" <?php echo $checked; ?>/>&nbsp;<?php echo $label_text; ?>
        </label>
        <?php    
    }
}