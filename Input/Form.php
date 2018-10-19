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
 * The Form Class
 *
 * @since 1.0.0
 */
class Form extends Field
{
    const Input_Type = 'form';

    /**
     * The Constructor
     *
     * @param int|WP_Post $form
     */
    public function __construct( $name )
    {
        parent::__construct( $name );
    }

    /**
     * Get all the *default* attributes of the form
     *
     * @return string, empty string if unset
     */
    public function get_attributes_default()
    {
        $default = array(
            'type'              => self::Input_Type,
            'name'              => '0',
            'action'            => htmlspecialchars( $_SERVER['PHP_SELF'] ),
            'method'            => 'post',
            'enctype'           => 'multipart/form-data',
            'class'             => '',
            'class_panel'       => '',
        );

        return $default;
    }

    /**
     * Extract object's value from post data
     *
     * @return input value
     */
    public function get_value( $post )
    {
        if ( is_empty( $post ) )
        {
            return '';
        }

        foreach ( $this->get_fields() as $field )
        {
            $name = $field->get_name();
            $value = $field->get_value( $post );

            $values[ $name ] = $value;
        }

        return $values;
    }

    /**
     * Verify status of input data
     *
     * @return True if value meets criteria
     */
    public function validate( $post )
    {
        if ( is_empty( $post ) )
        {
            return false;
        }

        foreach ( $this->get_fields() as $field )
        {
            if ( ! $field->validate( $post ) )
            {
                return False;
            }
        }
        return True;
    }

    public function render( )
    {
        $id             = esc_attr( $this->get_attribute( 'id' )            );
        $action         = esc_attr( $this->get_attribute( 'action' )        );
        $method         = esc_attr( $this->get_attribute( 'method' )        );
        $enctype        = esc_attr( $this->get_attribute( 'enctype' )       );
        $css            = esc_attr( $this->get_attribute( 'class' )         );
        $css_panel      = esc_attr( $this->get_attribute( 'class_panel' )   );

        ?>	
        <form 
            id="<?php echo $id ?>"
            class="<?php echo $css ?>"
            action="<?php echo $action ?>"
            method="<?php echo $method ?>"
            enctype="<?php echo $enctype ?>"
        >
            <div
                id="<?php echo $id . '_panel' ?>"
                class="<?php echo $css_panel ?>"
            >
                <?php
                foreach ( $this->get_fields() as $field)
                {
                    $field->html_print();
                }
                ?>
            </div>
        </form>
        <?php
    }

    /*-------------------------------------------------------------------------*/
    /**
     * Form fields
     *
     * @var array
     */
    protected $form_fields = array();

    /**
     * Get all form fields of this form
     *
     * @return array
     */
    public function get_fields()
    {
        return $this->form_fields;
    }

    /**
     * Add a form field to this form
     *
     * @return array
     */
    public function add_field( $field )
    {
        if ( ! is_null( $field ) )
        {
            $name = $field->get_attribute( 'name' );
            $this->form_fields[ $name ] = $field;
        }
    }
    /*-----------------------------------------------------------------------*/

    public function html_print( )
    {
        return $this->render();
    }

    /*-----------------------------------------------------------------------*/

    public function get_values( $post = null )
    {
        $values = array();

        if ( is_null( $post ) )
        {
            if ( $this->get_attribute( 'method' ) === 'post' )
            {
                // $_SERVER["REQUEST_METHOD"] will not exist if the form hasn't been submitted
                if ( $_SERVER["REQUEST_METHOD"] === "POST" )
                {
                    $post = $_POST;
                }
            }
            else if ( $this->get_attribute( 'method' ) === 'get' )
            {
                $post = $_GET;
            }
        }
        return $this->get_value( $post );  
    }
}
?>