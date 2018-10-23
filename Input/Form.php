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
    private $validation_errors = [];

    /**
     * Get all the *default* attributes of the form
     *
     * @return string, empty string if unset
     */
    public function get_attributes_default()
    {
        $default = array(
            'type'              => self::Input_Type,
            'name'              => 'form0',
            'action'            => '#', // submit data to same page
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
        $values = array();

        if ( empty( $post ) )
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
        $this->validation_errors = [];

        if ( empty( $post ) )
        {
            $this->validation_errors[ $this->get_name() ] =
            [
                'field'         => $this,
                'error'         => '$post is empty',
            ];
        }
        else
        {
            foreach ( $this->get_fields() as $field )
            {
                $error = $field->validate( $post );

                if ( ! is_null( $error ) )
                {
                    $this->validation_errors[ $error->get_error_object_name() ] = $error;
                }
            }
        }

        return empty( $this->validation_errors );
    }

    /**
     * Get all errors detected from last call to validate()
     *
     * @return array
     */
    public function get_validate_errors()
    {
        return $this->validation_errors;
    }

    public function render_fields( )
    {
        $id             = $this->get_attribute( 'id' );
        $css_panel      = $this->get_attribute( 'class_panel' );

        ?>	
        <div
            <?php Field::html_print_attribute('id',     $id . '_panel') ?>
            <?php Field::html_print_attribute('class',  $css_panel) ?>
        >
            <?php
            foreach ( $this->get_fields() as $field)
            {
                $field->html_print();
            }
            ?>
        </div>

        <?php
    }

    public function render( )
    {
        $name           = $this->get_attribute( 'name' );
        $id             = $this->get_attribute( 'id' );
        $action         = $this->get_attribute( 'action' );
        $method         = $this->get_attribute( 'method' );
        $enctype        = $this->get_attribute( 'enctype' );
        $css            = $this->get_attribute( 'class' );
        $css_panel      = $this->get_attribute( 'class_panel' );

        ?>	
        <form 
            <?php Field::html_print_attribute('name',       $name) ?>
            <?php Field::html_print_attribute('id',         $id) ?>
            <?php Field::html_print_attribute('action',     $action) ?>
            <?php Field::html_print_attribute('method',     $method) ?>
            <?php Field::html_print_attribute('enctype',    $enctype) ?>
        >
        <?php $this->render_fields(); ?>
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
            $field->set_form_id( $this->get_name( ) );

            $field_name = $field->get_name( );
            $this->form_fields[ $field_name ] = $field;
        }
    }

    /**
     * Get the input field matching the name
     *
     * @param string $name, name of field to find
     * @return field
     */
    public function get_field( $name )
    {
        foreach ( $this->get_fields() as $field )
        {
            if ( $field->get_name() === $name )
            {
                return $field;
            }
        }
    }
    /**
     * Set the values of the named input fields
     *
     * @return null
     */
    public function set_field_values( $field_values )
    {
        if ( ! empty( $field_values ) )
        {
            foreach ($field_values as $field_name => $value )
            {
                $field = $this->get_field( $field_name );
                if ( isset( $field ) )
                {
                    $field->set_attribute( 'value', $value );
                }
            }
        }
    }

    /**
     * Get the submit button associated with this form
     *
     * @return submit button object
     */
    public function get_submit_button( )
    {
        foreach ( $this->get_fields() as $field )
        {
            // May need to expand if we support > 1 button type
            if ( $field->get_type() === 'button'
                 &&
                 $field->get_attribute( 'button-type' ) === 'submit'
               )
            {
                return $field;
            }
        }
        return null;
    }

    /**
     * Return either the GET or POST data, depending on the form submission
     *
     * @return array of posted data
     */
    public function get_submit_data( )
    {
        $button = $this->get_submit_button();
        if ( ! isset( $button ) )
        {
            return;
        }

        if ( $this->get_attribute( 'method' ) === 'post'
             &&
             isset( $_POST[ $button->get_name() ] )
            )
        {
            return $_POST;
        }
        else if ( $this->get_attribute( 'method' ) === 'get'
             &&
             isset( $_GET[ $button->get_name() ] )
            )
        {
            return $_GET;
        }
        return;
    }
    /*-----------------------------------------------------------------------*/

    public function html_print( )
    {
        return $this->render();
    }

    /*-----------------------------------------------------------------------*/

    public function get_form_values( $post = null )
    {
        if ( is_null( $post ) )
        {
            $post = $this->get_submit_data( );
        }
        $values = $this->get_value( $post );  

        \Wkwgs_Logger::log_function( 'Form::get_form_values');
        \Wkwgs_Logger::log_var( '$this->get_attribute( "method" )', $this->get_attribute( 'method' ) );
        \Wkwgs_Logger::log_var( '$post', $post );
        \Wkwgs_Logger::log_var( '$values', $values );

        return $values;
    }
}
?>