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

include_once('Constants.php');
include_once(__DIR__ . '/../Wkwgs_Logger.php' );

include_once('HtmlHelper.php');
include_once('Base.php');

/*-------------------------------------------------------------------------*/
/* Interfaces */
/*-------------------------------------------------------------------------*/

/**
 * Attributes are a collection of name/value pairs
 * Attributes used by the HTML generation code are
 * split into two: values used for the HTML element and the others.
 *
 */
interface IAttributes2
{
    /**
     * Set the attributes of both types
     *
     * @param $attributes associative array of values, contains both types
     * @return null
     */
    public function set_attributes( array $attributes, array $default = null, array $alternate = null );

    /**
     * Get the attributes that are not in $alternate
     *
     * @return array, current attributes
     */
    public function get_attributes() : array;

    /**
     * Get the attributes that are in $alternate
     *
     * @return indexed array of the alternate values
     */
    public function get_attributes_alternate() : array;

    /**
     * Get the value of a single attribute
     *
     * @return mixed, value of $name. Empty string if unset
     */
    public function get_attribute( string $name );

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute( string $name, $value );
}

interface IAttributeProvider
{
    public function get_attributes_defaults() : array;

    public function get_attributes_alternate() : array;
}

/*-------------------------------------------------------------------------*/
/* Manage a collection of key/value pairs (aka HTML attributes) */
/*-------------------------------------------------------------------------*/

class Attributes2 implements IAttributes2, IHtmlPrinter
{

    public function __construct( array $attributes, array $default = [], array $alternate = [] )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->set_attributes($attributes, $default, $alternate);
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
    public function set_attributes( array $attributes, array $default = null, array $alternate = null )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->invalidate_cache();

        $this->attributes   = $attributes;
        $this->default      = $default;
        $this->alternate    = $alternate;
    }

    /**
     * Get the complete list of attributes, with default values set
     *
     * @return array, current attributes
     */
    public function get_attributes() :array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $attributes = $this->get_cache()[0];

        $logger->log_return( $attributes );
        return $attributes;
    }

    /**
     * Get the alternate attributes for this input object
     *
     * @return indexed array of the alternate values
     */
    public function get_attributes_alternate() : array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $attributes = $this->get_cache()[1];

        $logger->log_return( $attributes );
        return $attributes;
    }

    /**
     * Get the value of a single attribute of the field
     *
     * @return string
     */
    public function get_attribute( string $name )
    {
        $attributes = $this->get_attributes();

        $attr = '';
        if ( isset( $attributes[ $name ] ) )
        {
            $attr = $attributes[ $name ];
        }

        return $attr;
    }

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute( string $name, $value )
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        // Clear the cached value
        $this->attributes_combined_cached = null;

        if ( is_null( $this->attributes ) )
        {
            $this->attributes = [];
        }
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
    public function get_html()
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $attributes = $this->get_attributes();

        $html = '';

        if ( ! empty( $attributes ) )
        {
            foreach ( $attributes as $attribute => $value )
            {
                $attribute_html = Helper::get_html_attribute( $attribute, $value );
                if ( ! empty( $attribute_html ) )
                {
                    $html .= ' ';
                    $html .= $attribute_html;
                }
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
    protected       $alternate;
    protected       $cached;

    protected function invalidate_cache()
    {
        $this->cached = null;
    }
    protected function get_cache()
    {
        if ( is_null( $this->cached) )
        {
            $alternate      = [];
            $remaining      = array_merge( $this->default, $this->attributes );

            // If we have alternate attributes, split them out
            if ( ! is_null( $this->alternate ) )
            {
                foreach ( array_values( $this->alternate ) as $needle )
                {
                    if ( isset( $remaining[ $needle ] ) )
                    {
                        // copy entry to $alternate, and remove from $remaining
                        $alternate[ $needle ] = $remaining[ $needle ];
                        unset( $remaining[ $needle ] );
                    }
                }
            }

            $this->cached = [ $remaining, $alternate ];
        }

        return $this->cached;
    }
}
