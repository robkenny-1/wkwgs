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

namespace Input\HtmlHelper;

// Exit if accessed directly
defined( 'ABSPATH' ) || exit;

include_once('Constants.php');
include_once('Field.php');

class Form extends Element  implements IHtmlForm
{
    const Input_Type            = 'Form';
    const Default_Attributes    = array(
            'name'              => 'form0',
            'action'            => '#', // submit data to same page
            'method'            => 'post',
            'enctype'           => 'multipart/form-data',
        );

    public function __construct( $desc )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        
        if ( gettype( $desc ) !== 'array' )
        {
            $logger->log_var( '$desc is not an array', $desc );
            return;
        }

        $desc[ 'tag' ] = self::Input_Type;
        parent::__construct( $desc );

        $this->get_attributes()->set_attributes_default( self::Default_Attributes );
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlForm routines */
    /*-------------------------------------------------------------------------*/

    public function validate( $post )
    {
        $this->validation_errors = [];

        if ( empty( $post ) )
        {
            $this->validation_errors[ $this->get_attributes()->get_name() ] =
            [
                'field'         => $this,
                'error'         => '$post is empty',
            ];
        }
        else
        {
            foreach ( $this->get_children() as $field )
            {
                if ( $field instanceof IHtmlForm )
                {
                    $error = $field->validate( $post );

                    if ( ! is_null( $error ) )
                    {
                        $this->validation_errors[ $error->get_error_object_name() ] = $error;
                    }
                }
            }
        }

        return empty( $this->validation_errors );
    }

    public function get_value( $post )
    {
        $values = array();

        if ( ! empty( $post ) )
        {
            foreach ( $this->get_children() as $field )
            {
                if ( $field instanceof IHtmlForm )
                {
                    $name  = $field->get_attributes()->get_name();
                    $value = $field->get_value( $post );

                    $values[ $name ] = $value;
                }
            }
        }

        return $values;
    }

    protected $validation_errors;

    public function get_validate_errors()
    {
        return $this->validation_errors;
    }

    /**
     * Get the assigned form identity
     *
     * @return string
     */
    public function get_form_id()
    {
        return $this->get_attributes()->get_attribute( 'form' );
    }

    /**
     * Set the identity of the owning form
     *
     * @return string
     */
    public function set_form_id( $form_id )
    {
        $this->get_attributes()->set_attribute( 'form', $form_id );
    }
}
?>