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

    function __construct()
    {
        parent::__construct( __( 'Checkbox', DOMAIN ), 'checkbox_field' );
    }

    /**
     * Attributes of a checkbox
     *
     * @return array
     */
    public function get_attributes_default_for_class( )
    {
        return array(
            'checked'          => 'no',
        );    
    }

    /**
     * Render of the field in the frontend
     * This spits out the necessary HTML
     *
     * @return void
     */
    public function render( $form_id )
    {
        $name           = $this->attributes['name'];
        $label          = $this->attributes['label'];
        $checked        = $this->attributes['checked'] === 'yes' ? 'checked' : '';

        ?>
        <label>
            <input type="checkbox"
                name="<?php echo $name; ?>"
                value="yes"
                <?php echo $checked; ?>>
            <?php echo $label; ?>
        </label>
        <?php    
    }
}