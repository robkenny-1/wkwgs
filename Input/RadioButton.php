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

/**
 * The checkbox input class
 *
 * @since 1.0.0
 */
class RadioButton extends InputElement
{
    const Tag_Type              = 'input';
    const Default_Attributes    = [
        'type'                  => 'radio',
    ];
    const Alternate_Attributes  = [
        'label',
        'choices',
        'selected',
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
    /* IHtmlPrinter routines */
    /*-------------------------------------------------------------------------*/

    public function get_html_just_me()
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $html = '';

        $attributes     = $this->get_attributes()->get_attributes();
        $alternates     = $this->get_attributes()->get_attributes_alternate();
        $logger->log_var( '$attributes',    $attributes );
        $logger->log_var( '$alternates',    $alternates );

        if ( ! isset( $alternates[ 'choices' ] ) || empty( $alternates[ 'choices' ] ) )
        {
            $html .= parent::get_html_just_me();
        }
        else
        {
            $selected_value = $alternates[ 'selected' ];
            $logger->log_var( '$selected_value', $selected_value );

            foreach ( $alternates[ 'choices' ] as $value => $label  )
            {
                $logger->log_var( '$label', $label );
                $logger->log_var( '$value', $value );

                $attributes[ 'value' ]      = $value;
                $attributes[ 'label' ]      = $label;
                $attributes[ 'checked' ]    = $value === $selected_value;

                $rb = new RadioButton([
                    'attributes'                => $attributes,
                    'contents'                  => [],
                ]);

                $html .= $rb->get_html();
            }
        }

        $logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* InputElement routines */
    /*-------------------------------------------------------------------------*/

    public function validate_post( string $name, array $post ) : array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $this->validation_errors = [];

        // Perform data validation

        $raw        = $post[ $name ] ?? '';
        $required   = $this->get_attributes()->get_attribute( 'required' );
        $choices     = $this->get_choices();
        $logger->log_var( '$raw',           $raw );
        $logger->log_var( '$required',      $required );
        $logger->log_var( '$choices',       $choices );

        if ( empty( $choices ) )
        {
            $this->validation_errors[] = new HtmlValidateError(
                'RadioButton definition error: choices must not be empty', $name, $this                
            );         
        }
        else
        {
            if ( empty( $raw ) )
            {
                if ( Helper::is_true( $required ) )
                {
                    $this->validation_errors[] = new HtmlValidateError(
                        'required value missing', $name, $this                
                    );
                }
            }
            else if ( ! in_array( $raw, $choices, True ) )
            {
                $this->validation_errors[] = new HtmlValidateError(
                    '$post value does not match expected', $name, $this                
                );         
            }
        }

        $logger->log_return( $this->validation_errors );
        return $this->validation_errors;
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