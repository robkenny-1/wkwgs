<?php
/*
    Input Copyright (C) 2018 Rob Kenny

    Input is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Input is distributed in the hope that it will be useful,
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

/*-------------------------------------------------------------------------*/
/* Manage a collection of key/value pairs (aka HTML attributes) */
/*-------------------------------------------------------------------------*/

class Attributes implements IAttributes, IHtmlPrinter
{
    public function __construct( array $attributes, array $default = [], array $compound = [] )
    {
        $this->set_attributes($attributes, $default, $compound);
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributes routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Set the attributes for this input object,
     * this overrides any previous attributes
     *
     * @return void
     */
    public function set_attributes( array $attributes, array $default = [], array $compound = [] )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->invalidate_cache();

        $this->attributes   = $attributes               ?? [];
        $this->default      = $default                  ?? [];
        $this->compound     = array_values($compound)   ?? [];

        //$logger->log_var( '$this->attributes', $this->attributes );
        //$logger->log_var( '$this->default',    $this->default );
        //$logger->log_var( '$this->compound',  $this->compound );
    }

    /**
     * Get the complete list of attributes, with default values set
     *
     * @return array, current attributes
     */
    public function get_attributes() : array
    {
        // = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->update_cache();
        $attributes = $this->cached;

        //->log_return( $attributes );
        return $attributes;
    }

    /**
     * Get the compound attributes for this input object
     *
     * @return indexed array of the compound values
     */
    public function get_attributes_compound() : array
    {
        // = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->update_cache();
        $attributes = $this->cached_compound;

        //->log_return( $attributes );
        return $attributes;
    }

    /**
     * Get the value of a single attribute of the field
     *
     * @return string
     */
    public function get_attribute( string $name )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        
        $attribute = $this->get_attributes()[ $name ] ?? '';

        //$logger->log_return( $attribute );
        return $attribute;
    }

    /**
     * Get the value of a single compound attribute
     *
     * @return mixed, value of $name. Empty string if unset
     */
    public function get_attribute_compound( string $name )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        
        $attribute = $this->get_attributes_compound()[ $name ] ?? '';

        //$logger->log_return( $attribute );
        return $attribute;
    }

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute( string $name, $value )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->invalidate_cache();

        $this->attributes[ $name ] = $value;
    }

    /*-------------------------------------------------------------------------*/
    /* IHtmlPrinter routines */
    /*-------------------------------------------------------------------------*/
    
    /**
     * Get the HTML that represents the current Attributes
     *
     * @return string
     */
    public function get_html() : string
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $attributes = $this->get_attributes();

        $html = '';

        foreach ( $attributes as $attribute => $value )
        {
            $attribute_html = Helper::get_html_attribute( $attribute, $value );
            if ( ! empty( $attribute_html ) )
            {
                $html .= ' ';
                $html .= $attribute_html;
            }
        }

        $logger->log_return( $html );
        return $html;
    }

    /*-------------------------------------------------------------------------*/
    /* Implementation */
    /*-------------------------------------------------------------------------*/
    /*
     * Current attributes
     */
    protected       $attributes;
    protected       $default;
    protected       $compound;
    protected       $cached;
    protected       $cached_compound;

    protected function invalidate_cache()
    {
        $this->cached = null;
        $this->cached_compound = null;
    }
    
    protected function update_cache()
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        //$logger->log_var( '$this->attributes', $this->attributes );
        //$logger->log_var( '$this->compound',  $this->compound );

        if ( is_null( $this->cached) || is_null( $this->cached_compound) )
        {
            $compound      = [];
            $remaining      = array_merge( $this->default, $this->attributes );

            // If we have compound attributes, split them out
            if ( ! is_null( $this->compound ) )
            {
                foreach ( $this->compound as $alt )
                {
                    //$logger->log_var( '$alt', $alt );
                    if ( isset( $remaining[ $alt ] ) )
                    {
                        //$logger->log_msg( 'Moving from $remaining to $compound' );
                        // move $alt from $remaining to $compound
                        $compound[ $alt ] = $remaining[ $alt ];
                        unset( $remaining[ $alt ] );
                    }
                }
            }
            //$logger->log_var( '$remaining',  $remaining );
            //$logger->log_var( '$compound', $compound );

            $this->cached = $remaining;
            $this->cached_compound = $compound;
        }
    }
}
