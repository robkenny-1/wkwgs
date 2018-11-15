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

class Form extends Element implements IHtmlForm
{
    const Tag_Type              = 'form';
    const Default_Attributes    = [
        'name'              => 'form0',
        'action'            => '#', // submit data to same page
        'method'            => 'post',
        'enctype'           => 'multipart/form-data',
    ];
    const Alternate_Attributes = [
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

        $button = $this->get_submit_button();
        if ( ! isset( $button ) )
        {
            \Wkwgs_Logger::log_msg( 'No submit button found' );
        }
        else
        {
            $submit_type = $this->get_attributes()->get_attribute( 'method' );
            $button_name = $button->get_name();

            if ( $submit_type === 'post' && isset( $_POST[ $button_name ] ) )
            {
                $submit =  $_POST;
            }
            else if ( $submit_type === 'get' && isset( $_GET[ $button_name ] ) )
            {
                $submit =  $_GET;
            }
        }

        $logger->log_return( $submit );
        return $submit;
    }

    /**
     * Get the submit button associated with this form
     *
     * @return submit button object
     */
    public function get_submit_button( ) : ?IHtmlElement
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        foreach ( $this->get_children() as $field )
        {
            $tag  = $field->get_tag();
            $type = $field->get_attributes()->get_attribute( 'type' );
            $logger->log_var( '$tag', $tag );
            $logger->log_var( '$type', $type );

            // May need to expand if we support > 1 button type
            if ( $field->get_tag() === 'button'
                 &&
                 $field->get_attributes()->get_attribute( 'type' ) === 'submit'
               )
            {
                return $field;
            }
        }
        return null;
    }

    public function has_duplicate_names() : bool
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $is_duplicate = False;

        $existing_names = [];

        foreach ( $this->get_children() as $field )
        {
            $logger->log_var( '$field instanceof IHtmlForm', ($field instanceof IHtmlForm) ? 'True' : 'False' );
            $logger->log_var( '$field instanceof IHtmlElement', ($field instanceof IHtmlElement) ? 'True' : 'False' );

            // Need IHtmlElement for get_name()
            // Only IHtmlForm need to have unique names
            if ( $field instanceof IHtmlForm && $field instanceof IHtmlElement )
            {
                $name = $field->get_name();
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

    public function get_attributes_defaults() : array
    {
        $parent = parent::get_attributes_defaults();
        return array_merge( $parent, self::Default_Attributes );
    }

    public function get_attributes_alternate() : array
    {
        $parent = parent::get_attributes_alternate();
        return array_merge( $parent, self::Alternate_Attributes );
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlForm routines */
    /*-------------------------------------------------------------------------*/

    public function validate( array $post ) : array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->validation_errors = [];

        $name = $this->get_name();

        if ( empty( $post ) )
        {
            $this->validation_errors[] = new HtmlValidateError(
                '$post is empty', $name, $this                
            );
        }
        else if ( $this->has_duplicate_names() )
        {
            $this->validation_errors[] = new HtmlValidateError(
                'input objects do not all have unique names', $name, $this                
            );        
        }
        else
        {
            $save = [];

            foreach ( $this->get_children() as $field )
            {
                if ( $field instanceof IHtmlForm )
                {
                    $errors = $field->validate( $post );
                    $save[] = $errors;

                    if ( ! empty( $errors ) )
                    {
                        $this->validation_errors = array_merge( $this->validation_errors, $errors );
                    }
                }
            }
            $logger->log_msg( 'Saved validation errors');
            foreach ( $save as $ss )
            {
                $logger->log_var( '$ss', $ss );
            }
        }

        $logger->log_return( $this->validation_errors );
        return $this->validation_errors;
    }

    public function get_validate_errors() : array
    {
        return $this->validation_errors ?? [];
    }

    public function get_value( array $post )
    {
        $values = array();

        if ( ! empty( $post ) )
        {
            foreach ( $this->get_children() as $field )
            {
                if ( $field instanceof IHtmlForm )
                {
                    $name  = $field->get_attributes()->get_attribute( 'name' );
                    if ( ! empty( $name ) )
                    {
                        $value = $field->get_value( $post );
                        $values[ $name ] = $value;
                    }
                }
            }
        }

        return $values;
    }

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id() : string
    {
        return $this->get_attributes()->get_attribute( 'form' );
    }

    /**
     * Set the identity of the owning form
     *
     * @return string
     */
    public function set_form_id( string $form_id )
    {
        $this->get_attributes()->set_attribute( 'form', $form_id );
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