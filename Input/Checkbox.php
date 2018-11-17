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
class Checkbox extends InputElement
{
    const Tag_Type              = 'input';
    const Default_Attributes    = [
        'type'                  => 'checkbox',
        'checked'               => False,
        'value'                 => 'True',
    ];
    const Alternate_Attributes  = [
        'label',
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
    /* InputElement routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Validate data for a checkbox
     *
     * @return array of HtmlValidateError, empty if no errors
     */
    public function validate_post( string $name, array $post ) : array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        $ve = [];

        // Perform data validation

        $raw        = $post[ $name ] ?? '';
        $value      = $this->get_attributes()->get_attribute( 'value' );
        $logger->log_var( '$raw',           $raw );
        $logger->log_var( '$value',         $value );

        if ( empty( $value ) )
        {
            $ve[] = new HtmlValidateError(
                'checkbox definition error: value must not be empty', $name, $this                
            );         
        }
        else if ( !empty($raw) && $value !== $raw )
        {
            $ve[] = new HtmlValidateError(
                '$post value does not match expected', $name, $this                
            );         
        }

        $logger->log_return( $ve );
        return $ve;
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