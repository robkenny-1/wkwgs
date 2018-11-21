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

class Attributes implements IAttributeSeconday, IHtmlPrinter
{
    public function __construct( array $attributes, array $default = [], array $seconday = [] )
    {
        $this->set_attributes($attributes, $default );
        $this->set_attribute_seconday($seconday);
    }

    /*-------------------------------------------------------------------------*/
    /* IAttribute routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Set the attributes of both types
     *
     * @param array $attributes associative array of attribute name/value
     * @return null
     */
    public function set_attributes( array $attributes, array $default = [] )
    {
        $this->invalidate_cache();

        $this->attributes   = $attributes   ?? [];
        $this->default      = $default      ?? [];
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
     * Get the value of a single attribute of the field
     *
     * @return string
     */
    public function get_attribute( string $attribute )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        
        $attribute = $this->get_attributes()[ $attribute ] ?? '';

        //$logger->log_return( $attribute );
        return $attribute;
    }

    /**
     * Set the specified attribute
     *
     * @return void
     */
    public function set_attribute( string $attribute, $value )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->invalidate_cache();

        $this->attributes[ $attribute ] = $value;
    }

    /*-------------------------------------------------------------------------*/
    /* IAttributeSeconday routines */
    /*-------------------------------------------------------------------------*/

    /**
     * Define attributes that belong to seconday elements
     *
     * @param array $seconday non-associative array of attribute names
     * that exist for the seconday elements
     * @return null
     */
    public function set_attribute_seconday( array $seconday )
    {
        $this->invalidate_cache();
        $this->seconday = array_values($seconday)   ?? [];
    }

    /**
     * Get the seconday attributes for this input object
     *
     * @return indexed array of the seconday values
     */
    public function get_attributes_seconday() : array
    {
        // = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        $this->update_cache();
        $attributes = $this->cached_seconday;

        //->log_return( $attributes );
        return $attributes;
    }

    /**
     * Get the value of a single seconday attribute
     *
     * @return mixed, value of $attribute. Empty string if unset
     */
    public function get_attribute_seconday( string $attribute )
    {
        //$logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );
        
        $attribute = $this->get_attributes_seconday()[ $attribute ] ?? '';

        //$logger->log_return( $attribute );
        return $attribute;
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
    protected       $seconday;
    protected       $cached;
    protected       $cached_seconday;

    protected function invalidate_cache()
    {
        $this->cached = null;
        $this->cached_seconday = null;
    }
    
    protected function update_cache()
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        if ( is_null( $this->cached) || is_null( $this->cached_seconday) )
        {
            $secondary      = [];
            $attributes      = array_merge( $this->default, $this->attributes );

            foreach ( $this->seconday as $seconday_value )
            {
                if ( Helper::ends_with( $seconday_value, '-' ) )
                {
                    $seconday_name = substr( $seconday_value, 0, -1 );
                    $secondary[ $seconday_name ] = Attributes::move_values( $seconday_value, $attributes );
                }
                else
                {
                    if ( isset( $attributes[ $seconday_value ] ) )
                    {
                        // move $seconday_value from $attributes to $secondary
                        $secondary[ $seconday_value ] = $attributes[ $seconday_value ];
                        unset( $attributes[ $seconday_value ] );
                    }
                }
            }

            $logger->log_msg('UPDATED CACHE');
            $logger->log_var( '$attributes',  $attributes );
            $logger->log_var( '$secondary', $secondary );

            $this->cached = $attributes;
            $this->cached_seconday = $secondary;
        }
        else
        {
            $logger->log_return('<cache is up to date>');
        }
    }

    /*
     * If $seconday_value ends with a -, move all matching
     * to a label array in $seconday
     * Example:
     * $seconday_value = 'label-';
     * $attributes  = [ 'label-class' => 'aaa', 'style' => 'bbb', 'label-id' => 'ccc' ];
     * returns
     * [ 'class' => 'aaa', 'id' => 'ccc' ]
     * $attributes = [ 'style' => 'bbb' ];
     */
    public static function move_values( string $seconday_value, array & $attributes ) : array
    {
        $logger = new \Wkwgs_Function_Logger( __FUNCTION__, func_get_args(), get_class() );

        // Build the PCRE pattern
        $delim = '#';
        $pattern = $delim . '^' . preg_quote( $seconday_value, $delim ) . '(.*)$' . $delim;

        $moved = [];

        foreach( $attributes as $rr => $rr_value )
        {
            $matches = [];

            if ( preg_match( $pattern, $rr, $matches) === 1 )
            {
                $new_key = $matches[1];
                $logger->log_var( '$new_key', $new_key );

                $moved[ $new_key ] = $rr_value;
                unset( $attributes[ $rr ] );
            }
        }

        $logger->log_return( $moved );
        return $moved;
    }

    /**
     * Get and remove the named attribute from the array
     *
     * @return mixed Value of the attribute or $default
     */
    public static function get_attribute_and_remove( string $attribute, array & $attributes, string $default = '' )
    {
        $value = $default;

        if (isset($attributes[ $attribute ]))
        {
            $value = $attributes[ $attribute ];
            unset($attributes[ $attribute ]);
        }
    
        return $value;
    }
}
