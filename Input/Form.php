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

include_once('Input.php');

class Form extends Element implements IHtmlInput
{
    const Tag_Type              = 'form';
    const Attributes_Default    = [
        'name'              => 'form0',
        'action'            => '#', // submit data to same page
        'method'            => 'post',
        'enctype'           => 'multipart/form-data',
    ];
    const Attributes_Seconday = [
    ];

    public function __construct( $desc )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        
        if ( gettype( $desc ) !== 'array' )
        {
            $logger->log_var( '$desc is not an array', $desc );
            return;
        }

        $desc[ 'tag' ] = self::Tag_Type;
        parent::__construct( $desc );
    }

    /**
     * Return either the GET or POST data, depending on the form submission
     *
     * @return array of posted data
     */
    public function get_submit_data( ) : array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $submit = [];

        $form_name = $this->get_name();
            $submit_method = $this->get_attribute( 'method' );
        switch ( $submit_method )
        {
            case 'post':
                $submit =  $_POST;
                break;

            case 'get':
                $submit =  $_GET;
                break;

            default:
                $msg = "Form '$form_name': Unknown submit method: '$submit_method'";
                $logger->log_msg( $msg );
                throw new \Exception( $msg );
        }

        $logger->log_return( $submit );
        return $submit;
    }

    public function has_duplicate_names() : bool
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $is_duplicate = False;

        $existing_names = [];

        foreach ( $this as $child )
        {
            // Only IHtmlInput need to have unique names
            if ( $child instanceof IHtmlInput )
            {
                $name = $child->get_name();
                $logger->log_var( '$name', $name );

                if ( isset( $existing_names[ $name ] ) )
                {
                    $is_duplicate = True;
                    break;
                }

                $existing_names[ $name ] = True;
            }
        }

        $logger->log_return( $is_duplicate );
        return $is_duplicate;
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributeProvider routines */
    /*-------------------------------------------------------------------------*/

    public function define_attribute_default() : array
    {
        $parent = parent::define_attribute_default();
        return array_merge( $parent, self::Attributes_Default );
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributeSecondayProvider routines */
    /*-------------------------------------------------------------------------*/

    public function define_attribute_seconday() : array
    {
        $parent = parent::define_attribute_seconday();
        return array_merge( $parent, self::Attributes_Seconday );
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlInput routines */
    /*-------------------------------------------------------------------------*/

   /**
     * Get the type of Input
     *
     * @return  string Input type
     */
    public function get_type() : string
    {
        return $this->get_attribute( 'type' );
    }

    /**
     * Get the name of the HTML input element,
     * this is the index used to retrieve the data from POST
     *
     * @return string name of the input element
     */
    public function get_name() : string
    {
        return $this->get_attribute( 'name' );
    }

    /**
     * Verify that this object's data in $post is valid
     * This validation should be similar, if not exact, to the client side validation
     * This minimizes attacks that call POST directly
     *
     * @return array | list of validation errors or null if good
     */
    public function validate( array $post ) : array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $validation_errors = [];

        $name = $this->get_name();

        if ( empty( $post ) )
        {
            $validation_errors[] = new HtmlValidateError(
                '$post is empty', $name, $this                
            );
        }
        else if ( $this->has_duplicate_names() )
        {
            $validation_errors[] = new HtmlValidateError(
                'input objects do not all have unique names', $name, $this                
            );        
        }
        else
        {
            foreach ( $this as $child )
            {
                if ( $child instanceof IHtmlInput )
                {
                    $errors = $child->validate( $post );
                    if ( ! empty( $errors ) )
                    {
                        $validation_errors = array_merge( $validation_errors, $errors );
                    }
                }
            }
        }

        $logger->log_return( $validation_errors );
        return $validation_errors;
    }

    /**
     * Get this object's data in $post
     *
     * @return string | string contents of the input object
     */
    public function get_value( array $post )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $values = array();

        if ( ! empty( $post ) )
        {
            foreach ( $this as $child )
            {
                $logger->log_var( '$child', $child );

                if ( $child instanceof IHtmlInput )
                {
                    $name  = $child->get_attribute( 'name' );
                    if ( ! empty( $name ) )
                    {
                        $value = $child->get_value( $post );
                        $values[ $name ] = $value;
                    }
                }
            }
        }

        $logger->log_return( $values );
        return $values;
    }

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id() : string
    {
        return $this->get_attribute( 'form' );
    }

    /**
     * Set the identity of the owning form
     *
     * @return string
     */
    public function set_form_id( string $form_id )
    {
        $this->set_attribute( 'form', $form_id );
    }

    /*-------------------------------------------------------------------------*/
    /* InputElement routines */
    /*-------------------------------------------------------------------------*/

    public function validate_post( string $name, array $post ) : array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $validation_errors = [];

        // Perform data validation

        $logger->log_return( $validation_errors );
        return $validation_errors;
    }

    public function cleanse_data( $raw )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $cleansed = null;

        // No cleansing necessary?
        $cleansed = $raw;

        $logger->log_return( $cleansed );
        return $cleansed;
    }
}
?>